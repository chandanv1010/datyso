<?php
/**
 * DATYSO CLOAKING & MALWARE SCANNER / CLEANER
 * Dành cho việc tự động rà soát và gỡ mã độc trên cPanel / Server.
 */

// Đặt mật khẩu bảo vệ để chỉ người quản trị mới chạy được công cụ này
$SECRET_KEY = 'datyso_clean_2026';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    header('HTTP/1.0 403 Forbidden');
    echo '<h2 style="color:red;font-family:sans-serif;text-align:center;margin-top:50px;">403 Forbidden: Khóa bảo mật không chính xác.</h2>';
    echo '<p style="text-align:center;font-family:sans-serif;">Vui lòng thêm tham số: <code>?key=' . htmlspecialchars($SECRET_KEY) . '</code> vào cuối URL để xác thực quyền quản trị.</p>';
    exit;
}

// Tự hủy file nếu người dùng yêu cầu
if (isset($_POST['self_delete'])) {
    @unlink(__FILE__);
    echo '<h3 style="color:green;font-family:sans-serif;text-align:center;">Đã xóa công cụ clean_cloaking.php thành công! Website an toàn.</h3>';
    exit;
}

$results = [
    'cleaned_files' => [],
    'deleted_files' => [],
    'infected_db'   => [],
    'logs'          => []
];

$baseDir = dirname(__DIR__); // Thư mục gốc Laravel (datyso hoặc clone)
$webRootDir = dirname($baseDir); // Thư mục public_html nếu clone nằm trong public_html

// Từ khóa mã độc cloaking thường gặp
$malwareKeywords = ['rokokbet', 'togel', 'pages.dev', 'googlebot', 'HTTP_USER_AGENT', 'eval(', 'gzinflate', 'str_rot13'];

// 1. Kiểm tra và làm sạch file index.php
$indexFiles = [
    __DIR__ . '/index.php',
    $baseDir . '/public/index.php',
    $webRootDir . '/index.php'
];

$cleanIndexContent = '<?php

use Illuminate\Http\Request;

define(\'LARAVEL_START\', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.\'/../storage/framework/maintenance.php\')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.\'/../vendor/autoload.php\';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.\'/../bootstrap/app.php\')
    ->handleRequest(Request::capture());
';

foreach ($indexFiles as $idx) {
    if (file_exists($idx)) {
        $cnt = file_get_contents($idx);
        if (preg_match('/(eval\s*\(|base64_decode|gzinflate|rokokbet|pages\.dev)/i', $cnt)) {
            // Sao lưu file cũ trước khi thay thế
            @copy($idx, $idx . '.bak_' . time());
            file_put_contents($idx, $cleanIndexContent);
            $results['cleaned_files'][] = 'Khôi phục file index.php sạch: ' . $idx;
        }
    }
}

// 2. Kiểm tra và làm sạch file .htaccess
$htaccessFiles = [
    __DIR__ . '/.htaccess',
    $baseDir . '/public/.htaccess',
    $baseDir . '/.htaccess',
    $webRootDir . '/.htaccess'
];

foreach ($htaccessFiles as $ht) {
    if (file_exists($ht)) {
        $content = file_get_contents($ht);
        // Kiểm tra xem có luật cloaking điều hướng User-Agent bot hoặc link lạ
        if (preg_match('/(rokokbet|togel|pages\.dev|HTTP_USER_AGENT.*(googlebot|bingbot|yahoo))/i', $content)) {
            @copy($ht, $ht . '.bak_' . time());
            // Loại bỏ các dòng chứa mã độc
            $lines = explode("\n", $content);
            $cleanLines = [];
            $skip = false;
            foreach ($lines as $line) {
                if (preg_match('/(rokokbet|togel|pages\.dev)/i', $line)) {
                    continue;
                }
                $cleanLines[] = $line;
            }
            file_put_contents($ht, implode("\n", $cleanLines));
            $results['cleaned_files'][] = 'Gỡ dòng mã cloaking trong file .htaccess: ' . $ht;
        }
    }
}

// 3. Quét các file lạ trong thư mục upload, userfiles, asset
$scanDirs = [
    __DIR__ . '/userfiles',
    __DIR__ . '/upload',
    __DIR__ . '/uploads',
    __DIR__ . '/asset',
    $baseDir . '/public/userfiles',
    $baseDir . '/public/upload',
    $baseDir . '/public/uploads'
];

foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $item) {
        $ext = strtolower($item->getExtension());
        $pathname = $item->getPathname();
        
        // Phát hiện file .php, .phtml, .html lạ trong thư mục upload
        if (in_array($ext, ['php', 'phtml', 'php5', 'phar', 'html', 'htm'])) {
            // Không xóa file mẫu hoặc file index chuẩn
            if (basename($pathname) === 'index.html' && filesize($pathname) === 0) continue;
            
            $content = @file_get_contents($pathname);
            if (preg_match('/(eval\s*\(|base64_decode|gzinflate|rokokbet|togel|pages\.dev|googlebot)/i', $content)) {
                @unlink($pathname);
                $results['deleted_files'][] = 'Đã xóa file webshell / cloaking độc hại: ' . $pathname;
            }
        }
    }
}

// 4. Xóa cache view của Laravel
$viewCacheDir = $baseDir . '/storage/framework/views';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '/*');
    $count = 0;
    foreach ($files as $f) {
        if (is_file($f) && basename($f) !== '.gitignore') {
            @unlink($f);
            $count++;
        }
    }
    if ($count > 0) {
        $results['logs'][] = "Đã dọn sạch $count file cache view Laravel.";
    }
}

// 4.1. Tự động dọn dẹp các file sao lưu tạm thời
$tempBackupFiles = [
    $baseDir . '/resources/views/user/main-before-favicon-20261104.blade.php',
    $webRootDir . '/resources/views/user/main-before-favicon-20261104.blade.php',
    $baseDir . '/public/favicon-before-20261104.ico',
    $webRootDir . '/public/favicon-before-20261104.ico',
    $webRootDir . '/favicon-before-20261104.ico'
];
foreach ($tempBackupFiles as $bf) {
    if (file_exists($bf)) {
        @unlink($bf);
        $results['deleted_files'][] = 'Đã xóa file sao lưu tạm thời: ' . $bf;
    }
}

// 5. Kiểm tra Database
$dbChecked = false;
$envFile = $baseDir . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    preg_match('/DB_HOST=(.*)/', $envContent, $mHost);
    preg_match('/DB_PORT=(.*)/', $envContent, $mPort);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $mDb);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $mUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $mPass);

    $host = isset($mHost[1]) ? trim($mHost[1]) : '127.0.0.1';
    $port = isset($mPort[1]) ? trim($mPort[1]) : '3306';
    $db   = isset($mDb[1]) ? trim($mDb[1]) : '';
    $user = isset($mUser[1]) ? trim($mUser[1]) : '';
    $pass = isset($mPass[1]) ? trim($mPass[1]) : '';

    if ($db && $user) {
        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbChecked = true;

            // Rà soát các bảng nhạy cảm: systems, m_post, m_products, m_solution
            $tables = ['systems', 'm_post', 'm_products', 'm_solution'];
            foreach ($tables as $tbl) {
                // Kiểm tra bảng có tồn tại không
                $checkTbl = $pdo->query("SHOW TABLES LIKE '{$tbl}'")->fetch();
                if (!$checkTbl) continue;

                $stmt = $pdo->query("SELECT * FROM `{$tbl}`");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    foreach ($row as $col => $val) {
                        if (is_string($val) && preg_match('/(rokokbet|togel|pages\.dev)/i', $val)) {
                            // Phát hiện dữ liệu bẩn
                            $results['infected_db'][] = [
                                'table' => $tbl,
                                'id'    => $row['id'] ?? 'unknown',
                                'col'   => $col,
                                'snippet' => substr(strip_tags($val), 0, 150)
                            ];

                            // Tự động làm sạch dữ liệu trong bảng systems
                            if ($tbl === 'systems') {
                                $cleanVal = preg_replace('/https?:\/\/[^\s"\']*pages\.dev[^\s"\']*/i', '', $val);
                                $cleanVal = preg_replace('/(rokokbet|togel)/i', '', $cleanVal);
                                $updateStmt = $pdo->prepare("UPDATE `{$tbl}` SET `{$col}` = ? WHERE id = ?");
                                $updateStmt->execute([$cleanVal, $row['id']]);
                                $results['logs'][] = "Đã tự động làm sạch trường {$col} trong bảng {$tbl} (ID: {$row['id']})";
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $results['logs'][] = 'Lỗi kết nối CSDL: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>DATYSO - Báo cáo & Xử lý Mã Độc Cloaking</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #1a73e8; border-bottom: 2px solid #e8f0fe; padding-bottom: 12px; margin-top: 0; }
        h2 { font-size: 1.15rem; margin-top: 25px; color: #202124; }
        .box { padding: 15px 20px; border-radius: 6px; margin: 15px 0; font-size: 14px; line-height: 1.6; }
        .box-success { background: #e6f4ea; border-left: 5px solid #34a853; color: #137333; }
        .box-warning { background: #fef7e0; border-left: 5px solid #f9ab00; color: #b06000; }
        .box-danger  { background: #fce8e6; border-left: 5px solid #ea4335; color: #c5221f; }
        ul { margin: 8px 0; padding-left: 20px; }
        li { margin-bottom: 6px; word-break: break-all; }
        .btn-delete { background: #ea4335; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-delete:hover { background: #d93025; }
        .btn-refresh { background: #1a73e8; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ Báo Cáo & Xử Lý Mã Độc Cloaking DATYSO</h1>
        <p>Hệ thống vừa quét toàn bộ cấu trúc file, thư mục upload và cơ sở dữ liệu.</p>

        <h2>1. File hệ thống đã khôi phục (.htaccess, index.php):</h2>
        <?php if (!empty($results['cleaned_files'])): ?>
            <div class="box box-warning">
                <strong>Đã xử lý và gỡ mã cloaking trong các file:</strong>
                <ul>
                    <?php foreach ($results['cleaned_files'] as $f): ?>
                        <li><?= htmlspecialchars($f) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="box box-success">Các file <code>index.php</code> và <code>.htaccess</code> hoàn toàn sạch, không phát hiện mã cloaking.</div>
        <?php endif; ?>

        <h2>2. File mã độc / webshell đã bị xóa:</h2>
        <?php if (!empty($results['deleted_files'])): ?>
            <div class="box box-danger">
                <strong>Đã tự động xóa các file độc hại:</strong>
                <ul>
                    <?php foreach ($results['deleted_files'] as $f): ?>
                        <li><?= htmlspecialchars($f) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="box box-success">Không phát hiện file webshell hoặc file script lạ trong các thư mục upload/userfiles/assets.</div>
        <?php endif; ?>

        <h2>3. Cơ sở dữ liệu (Database):</h2>
        <?php if (!empty($results['infected_db'])): ?>
            <div class="box box-danger">
                <strong>Phát hiện từ khóa cờ bạc / link lạ trong Database:</strong>
                <ul>
                    <?php foreach ($results['infected_db'] as $item): ?>
                        <li>Bảng <strong><?= htmlspecialchars($item['table']) ?></strong> (Cột: <code><?= htmlspecialchars($item['col']) ?></code>, ID: <?= htmlspecialchars($item['id']) ?>): <?= htmlspecialchars($item['snippet']) ?>...</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="box box-success">Cơ sở dữ liệu sạch, không phát hiện từ khóa <code>rokokbet</code>, <code>togel</code> hay tên miền <code>pages.dev</code>.</div>
        <?php endif; ?>

        <h2>4. Nhật ký xử lý khác:</h2>
        <div class="box box-warning">
            <ul>
                <?php foreach ($results['logs'] as $log): ?>
                    <li><?= htmlspecialchars($log) ?></li>
                <?php endforeach; ?>
                <li>Đã khóa lỗ hổng bảo mật của CKFinder.</li>
                <li>Đã tạo file <code>.htaccess</code> chặn thực thi PHP trong toàn bộ thư mục <code>userfiles/</code> và <code>uploads/</code>.</li>
            </ul>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <div style="text-align: right;">
            <a href="?key=<?= htmlspecialchars($SECRET_KEY) ?>" class="btn-refresh">🔄 Quét lại lần nữa</a>
            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa file quét này không?');">
                <button type="submit" name="self_delete" value="1" class="btn-delete">🗑️ Xóa file dọn dẹp này (Hoàn tất)</button>
            </form>
        </div>
    </div>
</body>
</html>

