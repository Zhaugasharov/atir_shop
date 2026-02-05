<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Product;
use App\Models\KeyWord;
use App\Models\ProductKeyWord;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deleteProduct($id)
    {
        // Найти товар по ID
        $product = Product::find($id);

        if (!$product) {
            // Если товар не найден, можно вернуть с ошибкой
            return redirect()->back()->with('error', 'Товар не найден!');
        }

        // Удаляем товар
        $product->delete();

        return redirect()->back()->with('success', 'Товар успешно удален!');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Product::query()->with('keywords');

        // Фильтр по названию
        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Фильтр по полу
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        // Фильтр по ключевым словам
        if ($request->has('keywords') && $request->keywords != '') {
            $keywords = explode(',', $request->keywords);
            $keywords = array_map('trim', $keywords);

            $query->whereHas('keywords', function($q) use ($keywords) {
                $q->whereIn('name', $keywords);
            });
        }

        $data['products'] = $query->paginate(20);
        $data['request'] = $request;

        return view('home', $data);
    }

    public function order($order) {
        $data = [];

        return view('orders', $data);
    }

    public function saveProducts(Request $request)
    {
        if (
            !$request->hasFile('excel') ||
            !$request->file('excel')->isValid()
        ) {
            return redirect()->back()
                ->with('error', 'Файл не загружен или поврежден');
        }

        $file = $request->file('excel');
        $extension = strtolower($file->getClientOriginalExtension());

        $allowedExtensions = ['xlsx', 'xls', 'csv', 'txt'];
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->back()
                ->with('error', 'Используйте Excel (.xlsx, .xls) или CSV файл');
        }

        try {

            // ---------- ЧТЕНИЕ ФАЙЛА ----------
            if ($extension === 'csv' || $extension === 'txt') {
                $data = $this->parseCSVFile($file);
            } else {
                $data = $this->parseExcelFileSimple($file, $extension);
            }

            if (empty($data['rows'])) {
                throw new \Exception('Файл пуст');
            }

            // ---------- ПРОВЕРКА КОЛОНОК ----------
            $required = ['name', 'article', 'gender', 'keywords'];
            $header = $data['header'];
            $rows = $data['rows'];

            $missing = array_diff($required, $header);
            if (!empty($missing)) {
                throw new \Exception(
                    'Отсутствуют колонки: ' . implode(', ', $missing)
                );
            }

            $nameIndex     = array_search('name', $header);
            $articleIndex  = array_search('article', $header);
            $genderIndex   = array_search('gender', $header);
            $keywordsIndex = array_search('keywords', $header);

            $success = 0;
            $errors  = [];

            DB::beginTransaction();

            // ---------- ОБРАБОТКА СТРОК ----------
            foreach ($rows as $rowNumber => $row) {

                $line = $rowNumber + 2;

                try {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $name = $this->cleanValue($row[$nameIndex] ?? '');
                    if ($name === '') {
                        throw new \Exception('Пустое название товара');
                    }

                    $article = $this->cleanValue($row[$articleIndex] ?? '');
                    $gender  = $this->parseGender($row[$genderIndex] ?? '');
                    $keywordsRaw = $row[$keywordsIndex] ?? '';

                    $keywords = $this->parseKeywords($keywordsRaw);

                    // ---------- СОЗДАНИЕ ТОВАРА ----------
                    $product = Product::create([
                        'name'    => $name,
                        'article' => $article ?: null,
                        'gender'  => $gender,
                    ]);

                    // ---------- КЛЮЧЕВЫЕ СЛОВА ----------
                    $keywordIds = [];

                    foreach ($keywords as $word) {
                        $normalized = mb_strtolower(trim($word), 'UTF-8');

                        if ($normalized === '') {
                            continue;
                        }

                        $keyword = KeyWord::firstOrCreate(
                            ['name' => $normalized],
                            ['name' => $word]
                        );

                        $keywordIds[] = $keyword->id;
                    }

                    if (!empty($keywordIds)) {
                        $product->keywords()->syncWithoutDetaching($keywordIds);
                    }

                    $success++;

                } catch (\Exception $e) {
                    $errors[] = "Строка {$line}: {$e->getMessage()}";
                }
            }

            if ($success === 0) {
                DB::rollBack();

                throw new \Exception('Ни одна строка не была сохранена');
            }

            DB::commit();

            if (!empty($errors)) {
                session()->flash('import_errors', array_slice($errors, 0, 10));
            }

            return redirect()->back()->with(
                'success',
                "Импорт завершен: {$success} товаров, ошибок: " . count($errors)
            );

        } catch (\Exception $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return redirect()->back()
                ->with('error', 'Ошибка импорта: ' . $e->getMessage());
        }
    }

    /**
     * Парсит CSV файл
     */
    private function parseCSVFile($file)
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new \Exception('Не удалось открыть файл');
        }

        // Определяем разделитель
        $firstLine = fgets($handle);
        rewind($handle);

        if (strpos($firstLine, ',') !== false) {
            $delimiter = ',';
        } elseif (strpos($firstLine, ';') !== false) {
            $delimiter = ';';
        } elseif (strpos($firstLine, "\t") !== false) {
            $delimiter = "\t";
        } else {
            $delimiter = ',';
        }

        // Читаем заголовки
        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            throw new \Exception('Файл пуст или поврежден');
        }

        // Нормализуем заголовки
        $header = array_map(function($item) {
            return mb_strtolower(trim($item), 'UTF-8');
        }, $header);

        // Читаем остальные строки
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Очищаем значения
            $row = array_map([$this, 'cleanValue'], $row);
            $rows[] = $row;
        }

        fclose($handle);

        return [
            'header' => $header,
            'rows' => $rows
        ];
    }

    /**
     * Простой парсер Excel файлов (без использования тяжелых библиотек)
     * Для XLSX используем чтение как ZIP архива
     */
    private function parseExcelFileSimple($file, $extension)
    {
        $path = $file->getRealPath();

        if ($extension === 'xlsx') {
            // Для XLSX - читаем как ZIP архив
            return $this->parseXLSXFile($path);
        } elseif ($extension === 'xls') {
            // Для XLS - используем простой бинарный парсер
            return $this->parseXLSFile($path);
        }

        throw new \Exception('Неподдерживаемый формат Excel файла');
    }

    /**
     * Парсит XLSX файл (Office Open XML)
     */
    private function parseXLSXFile($path)
    {
        try {
            // XLSX - это ZIP архив с XML файлами
            $zip = new \ZipArchive();

            if ($zip->open($path) !== true) {
                throw new \Exception('Не удалось открыть Excel файл как ZIP архив');
            }

            // Ищем файл sharedStrings.xml и sheet1.xml
            $sharedStrings = [];
            $sheetData = '';

            // Читаем общие строки (sharedStrings.xml)
            if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                $sharedStringsContent = $zip->getFromName('xl/sharedStrings.xml');
                $sharedStringsXml = simplexml_load_string($sharedStringsContent);

                foreach ($sharedStringsXml->si as $si) {
                    $sharedStrings[] = (string)$si->t;
                }
            }

            // Читаем первый лист (обычно sheet1.xml)
            $sheetFile = 'xl/worksheets/sheet1.xml';
            if ($zip->locateName($sheetFile) === false) {
                // Пробуем найти любой sheet
                for ($i = 1; $i <= 10; $i++) {
                    $sheetFile = "xl/worksheets/sheet{$i}.xml";
                    if ($zip->locateName($sheetFile) !== false) {
                        break;
                    }
                }
            }

            if ($zip->locateName($sheetFile) === false) {
                $zip->close();
                throw new \Exception('Не найден файл листа в Excel файле');
            }

            $sheetContent = $zip->getFromName($sheetFile);
            $zip->close();

            // Парсим XML листа
            $sheetXml = simplexml_load_string($sheetContent);
            $namespaces = $sheetXml->getNamespaces(true);
            $sheetXml->registerXPathNamespace('ns', $namespaces['']);

            $rows = [];
            $maxCol = 0;

            foreach ($sheetXml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $cell) {
                    $cellRef = (string)$cell['r']; // например, A1, B1
                    $cellType = (string)$cell['t']; // тип ячейки
                    $value = (string)$cell->v;

                    // Получаем номер колонки из ссылки (A=1, B=2, ...)
                    preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                    if (count($matches) >= 2) {
                        $col = $this->columnToNumber($matches[1]);
                        $rowNum = (int)$matches[2];

                        // Обрабатываем значение
                        if ($cellType === 's') {
                            // Ссылка на общую строку
                            $index = (int)$value;
                            $rowData[$col] = isset($sharedStrings[$index]) ? $sharedStrings[$index] : '';
                        } else {
                            $rowData[$col] = $value;
                        }

                        if ($col > $maxCol) {
                            $maxCol = $col;
                        }
                    }
                }

                // Заполняем пустые ячейки
                for ($i = 1; $i <= $maxCol; $i++) {
                    if (!isset($rowData[$i])) {
                        $rowData[$i] = '';
                    }
                }

                ksort($rowData);
                $rows[] = array_values($rowData);
            }

            if (empty($rows)) {
                throw new \Exception('Excel файл пуст');
            }

            // Первая строка - заголовки
            $header = array_shift($rows);
            $header = array_map(function($item) {
                return mb_strtolower(trim($item), 'UTF-8');
            }, $header);

            // Очищаем строки
            foreach ($rows as &$row) {
                $row = array_map([$this, 'cleanValue'], $row);
            }

            return [
                'header' => $header,
                'rows' => $rows
            ];

        } catch (\Exception $e) {
            throw new \Exception('Ошибка чтения XLSX файла: ' . $e->getMessage());
        }
    }

    /**
     * Парсит старый XLS формат (Binary)
     * Использует простую обработку через PHP-ExcelReader или аналоги
     */
    private function parseXLSFile($path)
    {
        try {
            // Используем простой парсер для XLS
            // Можно использовать библиотеку PHPExcelReader, но для простоты
            // конвертируем в CSV через временный файл

            $tmpCsvPath = tempnam(sys_get_temp_dir(), 'xls_') . '.csv';

            // Используем команду ssconvert (из пакета gnumeric) если установлена
            if (function_exists('shell_exec') && `which ssconvert`) {
                shell_exec("ssconvert \"$path\" \"$tmpCsvPath\" 2>/dev/null");

                if (file_exists($tmpCsvPath) && filesize($tmpCsvPath) > 0) {
                    $result = $this->parseCSVFile(new \Illuminate\Http\UploadedFile(
                        $tmpCsvPath,
                        basename($tmpCsvPath),
                        'text/csv',
                        null,
                        true
                    ));

                    unlink($tmpCsvPath);
                    return $result;
                }
            }

            // Если не удалось конвертировать, предлагаем пользователю сохранить как CSV
            throw new \Exception(
                'Для файлов .xls рекомендуется сохранить их как CSV. ' .
                'В Excel: Файл → Сохранить как → CSV (разделители - запятые)'
            );

        } catch (\Exception $e) {
            throw new \Exception('Ошибка чтения XLS файла: ' . $e->getMessage());
        }
    }

    /**
     * Конвертирует буквенное обозначение колонки в число (A=1, B=2, ...)
     */
    private function columnToNumber($column)
    {
        $number = 0;
        $length = strlen($column);

        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        return $number;
    }

    /**
     * Очищает значение ячейки
     */
    private function cleanValue($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Преобразуем в строку
        $value = (string) $value;

        // Убираем лишние пробелы и переносы строк
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    /**
     * Парсит значение пола
     */
    private function parseGender($value)
    {
        if (empty($value)) {
            return 'unisex';
        }

        $value = mb_strtolower(trim($value), 'UTF-8');

        $maleValues = ['1', 'male', 'мужской', 'м', 'муж', 'мъж', 'm', 'мальчик', 'парень'];
        $femaleValues = ['2', 'female', 'женский', 'ж', 'жен', 'f', 'девушка', 'девочка'];
        $unisexValues = ['3', 'unisex', 'унисекс', 'у', 'уни', 'универсальный'];

        if (in_array($value, $maleValues)) {
            return 'male';
        } elseif (in_array($value, $femaleValues)) {
            return 'female';
        } elseif (in_array($value, $unisexValues)) {
            return 'unisex';
        }

        // Проверяем частичное совпадение
        if (mb_strpos($value, 'муж') !== false) {
            return 'male';
        } elseif (mb_strpos($value, 'жен') !== false) {
            return 'female';
        } elseif (mb_strpos($value, 'уни') !== false) {
            return 'unisex';
        }

        return 'unisex';
    }

    /**
     * Парсит строку ключевых слов
     */
    private function parseKeywords($string)
    {
        if (empty($string)) {
            return [];
        }

        $string = (string) $string;

        // Заменяем разные разделители на запятые
        $string = str_replace([';', '|', '/', '\\', '、', '，'], ',', $string);

        // Убираем лишние пробелы
        $string = preg_replace('/\s+/', ' ', $string);

        // Разделяем по запятым
        $keywords = explode(',', $string);

        // Очищаем и фильтруем
        $keywords = array_map('trim', $keywords);
        $keywords = array_filter($keywords, function($keyword) {
            return !empty($keyword) && strlen($keyword) > 0;
        });

        // Убираем дубликаты
        $keywords = array_unique($keywords);

        return $keywords;
    }

    public function saveProduct(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,unisex',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'keywords' => 'required|json'
        ]);

        DB::beginTransaction();

        try {
            // Получаем ключевые слова из JSON
            $keywords = json_decode($request->keywords, true);

            // Проверяем, есть ли продукт с таким артикулом
            $product = Product::where('id', $request->product_id)->first();

            if ($product) {
                // Обновляем существующий продукт
                $product->update([
                    'name' => $request->name,
                    'gender' => $request->gender,
                    'article' => $request->sku
                ]);
            } else {
                // Создаем новый продукт
                $product = Product::create([
                    'name' => $request->name,
                    'article' => $request->sku,
                    'gender' => $request->gender,
                    'image' => null
                ]);
            }

            // Обработка изображения
            if ($request->hasFile('image')) {

                // Удаляем старое изображение, если оно есть
                if ($product->image) {
                    Storage::delete($product->image);
                }

                // Сохраняем новое изображение
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
                $product->save();
            }

            // Получаем текущие ключевые слова продукта
            $currentKeywordIds = ProductKeyWord::where('product_id', $product->id)
                ->pluck('keyword_id')
                ->toArray();

            // Массив для новых ключевых слов
            $newKeywordIds = [];

            // Обрабатываем каждое ключевое слово
            foreach ($keywords as $keywordName) {
                $keywordName = trim($keywordName);

                if (empty($keywordName)) {
                    continue;
                }

                // Ищем или создаем ключевое слово
                $keyword = KeyWord::firstOrCreate(
                    ['name' => $keywordName],
                    ['name' => $keywordName]
                );

                $newKeywordIds[] = $keyword->id;
            }

            // Удаляем связи, которых нет в новых ключевых словах
            $keywordsToRemove = array_diff($currentKeywordIds, $newKeywordIds);
            if (!empty($keywordsToRemove)) {
                ProductKeyWord::where('product_id', $product->id)
                    ->whereIn('keyword_id', $keywordsToRemove)
                    ->delete();
            }

            // Добавляем новые связи, которых еще нет
            $keywordsToAdd = array_diff($newKeywordIds, $currentKeywordIds);
            foreach ($keywordsToAdd as $keywordId) {
                ProductKeyWord::firstOrCreate([
                    'product_id' => $product->id,
                    'keyword_id' => $keywordId
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', $product->wasRecentlyCreated ? 'Товар успешно создан' : 'Товар успешно обновлен');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ошибка при сохранении товара: ' . $e->getMessage());
        }
    }
}
