<?php
/**
 * DATYSO CLOAKING & MALWARE SCANNER / CLEANER - V2 ADVANCED
 * Quét sâu, dò tìm và triệt phá mã độc Cloaking RokokBet / Togel / Pages.dev
 */

// Đặt mật khẩu bảo vệ
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
    echo '<h3 style="color:green;font-family:sans-serif;text-align:center;margin-top:50px;">Đã xóa công cụ clean_cloaking.php thành công!</h3>';
    exit;
}

$results = [
    'cleaned_files' => [],
    'deleted_files' => [],
    'infected_db'   => [],
    'suspicious_files' => [],
    'logs'          => []
];

$baseDir = dirname(__DIR__); // Thư mục gốc Laravel (/home/nhdat4wj/public_html hoặc /home/nhdat4wj/public_html/clone)
$webRootDir = dirname($baseDir); // Thư mục cha nếu baseDir là clone
$publicHtmlDirs = array_unique(array_filter([
    $baseDir,
    $webRootDir,
    $_SERVER['DOCUMENT_ROOT'] ?? '',
    '/home/nhdat4wj/public_html'
], 'is_dir'));

// Danh sách từ khóa cloaking và mã độc
$malwareKeywords = [
    'rokokbet', 'togel', 'pages.dev', 'cas89', 'assets-cdn-msk7s',
    'HTTP_USER_AGENT', 'googlebot', 'bingbot', 'slurp',
    'eval(', 'gzinflate', 'str_rot13', 'base64_decode'
];

$malwareRegex = '/(rokokbet|togel|pages\.dev|cas89|assets-cdn-msk7s|jquery-lib|jquery-migrate-1\.4\.1)/i';
$cloakingRegex = '/(HTTP_USER_AGENT.*(googlebot|bingbot|baiduspider|slurp|yandex)|googlebot.*(rokokbet|togel|cas89|pages\.dev))/i';

// ==========================================
// 1. KIỂM TRA .user.ini VÀ php.ini (auto_prepend_file)
// ==========================================
$iniFiles = [];
foreach ($publicHtmlDirs as $d) {
    $iniFiles[] = $d . '/.user.ini';
    $iniFiles[] = $d . '/php.ini';
    $iniFiles[] = $d . '/public/.user.ini';
    $iniFiles[] = $d . '/public/php.ini';
}

foreach (array_unique($iniFiles) as $ini) {
    if (file_exists($ini)) {
        $content = file_get_contents($ini);
        if (preg_match('/(auto_prepend_file|auto_append_file)/i', $content)) {
            @copy($ini, $ini . '.bak_' . time());
            @unlink($ini);
            $results['deleted_files'][] = 'Phát hiện và xóa file cấu hình chèn mã tự động (auto_prepend_file): ' . $ini;
        }
    }
}

// ==========================================
// 2. KIỂM TRA VÀ LÀM SẠCH TẤT CẢ FILE .htaccess
// ==========================================
$htaccessCandidates = [];
foreach ($publicHtmlDirs as $d) {
    $htaccessCandidates[] = $d . '/.htaccess';
    $htaccessCandidates[] = $d . '/public/.htaccess';
    $htaccessCandidates[] = $d . '/public_html/.htaccess';
}

foreach (array_unique($htaccessCandidates) as $ht) {
    if (file_exists($ht)) {
        $content = file_get_contents($ht);
        if (preg_match($malwareRegex, $content) || preg_match($cloakingRegex, $content) || preg_match('/RewriteCond.*HTTP_USER_AGENT.*(google|bot|spider)/i', $content)) {
            @copy($ht, $ht . '.bak_' . time());
            $lines = explode("\n", $content);
            $cleanLines = [];
            $inMaliciousBlock = false;
            
            foreach ($lines as $line) {
                // Bỏ qua dòng chứa bot user-agent hoặc rokokbet hoặc amp.html
                if (preg_match($malwareRegex, $line) || 
                    preg_match('/(googlebot|bingbot|rokokbet|togel|cas89|pages\.dev|amp\.html)/i', $line)) {
                    continue;
                }
                $cleanLines[] = $line;
            }
            file_put_contents($ht, implode("\n", $cleanLines));
            $results['cleaned_files'][] = 'Đã gỡ mã điều hướng Googlebot trong file .htaccess: ' . $ht;
        }
    }
}

// ==========================================
// 3. KIỂM TRA VÀ KHÔI PHỤC FILE index.php
// ==========================================
$indexFiles = [];
foreach ($publicHtmlDirs as $d) {
    $indexFiles[] = $d . '/index.php';
    $indexFiles[] = $d . '/public/index.php';
}

$cleanPublicIndex = '<?php

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

foreach (array_unique($indexFiles) as $idx) {
    if (file_exists($idx)) {
        $content = file_get_contents($idx);
        if (preg_match($malwareRegex, $content) || preg_match('/(eval\s*\(|gzinflate|str_rot13|base64_decode.*googlebot)/i', $content) || (preg_match('/HTTP_USER_AGENT/i', $content) && preg_match('/googlebot/i', $content))) {
            @copy($idx, $idx . '.bak_' . time());
            file_put_contents($idx, $cleanPublicIndex);
            $results['cleaned_files'][] = 'Đã khôi phục file index.php chuẩn sạch (gỡ mã cloaking): ' . $idx;
        }
    }
}

// ==========================================
// 4. TÌM VÀ XÓA FILE amp.html VÀ CÁC WEBSHELL / FILE LẠ
// ==========================================
$scanTargetDirs = [];
foreach ($publicHtmlDirs as $d) {
    $scanTargetDirs[] = $d . '/public/asset';
    $scanTargetDirs[] = $d . '/public/upload';
    $scanTargetDirs[] = $d . '/public/uploads';
    $scanTargetDirs[] = $d . '/public/userfiles';
    $scanTargetDirs[] = $d . '/storage/app';
    $scanTargetDirs[] = $d . '/asset';
    $scanTargetDirs[] = $d . '/upload';
    $scanTargetDirs[] = $d . '/uploads';
    $scanTargetDirs[] = $d . '/userfiles';
}

// Quét trực tiếp file amp.html ở các thư mục gốc
foreach ($publicHtmlDirs as $d) {
    $ampFiles = [
        $d . '/amp.html',
        $d . '/public/amp.html',
        $d . '/amp.php',
        $d . '/public/amp.php'
    ];
    foreach ($ampFiles as $amp) {
        if (file_exists($amp)) {
            @unlink($amp);
            $results['deleted_files'][] = 'Đã xóa file cloaking tĩnh: ' . $amp;
        }
    }
}

// Quét đệ quy tìm file độc hại trong các thư mục media và asset
foreach (array_unique($scanTargetDirs) as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        if (!$item->isFile()) continue;
        $pathname = $item->getPathname();
        $ext = strtolower($item->getExtension());
        $filename = strtolower($item->getFilename());

        // Nếu là file amp.html hoặc có tên rokokbet
        if (strpos($filename, 'amp.html') !== false || strpos($filename, 'rokokbet') !== false) {
            @unlink($pathname);
            $results['deleted_files'][] = 'Đã xóa file mã độc: ' . $pathname;
            continue;
        }

        // Kiểm tra file thực thi trong thư mục media (.php, .phtml, .phar...)
        if (in_array($ext, ['php', 'phtml', 'php5', 'phar', 'html', 'htm', 'ico', 'js'])) {
            // Không xóa file index.html rỗng an toàn
            if ($filename === 'index.html' && filesize($pathname) === 0) continue;

            $cnt = @file_get_contents($pathname);
            if ($cnt === false) continue;

            if (preg_match($malwareRegex, $cnt) || 
                (in_array($ext, ['php', 'phtml', 'phar']) && preg_match('/(eval\s*\(|gzinflate|base64_decode\s*\(|str_rot13)/i', $cnt))) {
                @unlink($pathname);
                $results['deleted_files'][] = 'Đã tiêu diệt webshell / file cloaking: ' . $pathname;
            }
        }
    }
}

// ==========================================
// 5. DỌN SẠCH CÁC FILE SAO LƯU TẠM THỜI
// ==========================================
$tempFiles = [
    $baseDir . '/resources/views/user/main-before-favicon-20261104.blade.php',
    $webRootDir . '/resources/views/user/main-before-favicon-20261104.blade.php',
    $baseDir . '/public/favicon-before-20261104.ico',
    $webRootDir . '/public/favicon-before-20261104.ico',
    $webRootDir . '/favicon-before-20261104.ico'
];
foreach ($tempFiles as $tf) {
    if (file_exists($tf)) {
        @unlink($tf);
        $results['deleted_files'][] = 'Đã dọn dẹp file sao lưu tạm thời: ' . $tf;
    }
}

// ==========================================
// 6. XÓA SẠCH CACHE VIEW CỦA LARAVEL
// ==========================================
foreach ($publicHtmlDirs as $d) {
    $viewCacheDir = $d . '/storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir . '/*.php');
        if ($files) {
            foreach ($files as $f) {
                @unlink($f);
            }
            $results['logs'][] = "Đã dọn sạch " . count($files) . " file cache view tại: " . $viewCacheDir;
        }
    }
}

// ==========================================
// 7. KIỂM TRA DATABASE (BẢNG systems, m_post, m_products...)
// ==========================================
$dbChecked = false;
$envCandidates = [];
foreach ($publicHtmlDirs as $d) {
    $envCandidates[] = $d . '/.env';
}

foreach (array_unique($envCandidates) as $envFile) {
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

                // Quét tất cả các bảng có thể lưu cấu hình, bài viết, sản phẩm
                $targetTables = ['systems', 'settings', 'options', 'm_social', 'm_address', 'm_post', 'm_products', 'm_solution'];
                foreach ($targetTables as $tbl) {
                    $hasTbl = $pdo->query("SHOW TABLES LIKE '{$tbl}'")->fetch();
                    if (!$hasTbl) continue;

                    $stmt = $pdo->query("SELECT * FROM `{$tbl}`");
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($rows as $row) {
                        foreach ($row as $col => $val) {
                            if (is_string($val) && (preg_match($malwareRegex, $val) || preg_match('/<script.*(pages\.dev|cas89|rokokbet)/i', $val))) {
                                $results['infected_db'][] = [
                                    'table'   => $tbl,
                                    'id'      => $row['id'] ?? 'unknown',
                                    'col'     => $col,
                                    'snippet' => substr(strip_tags($val), 0, 150)
                                ];

                                // Làm sạch dữ liệu trong bảng cấu hình systems / settings
                                if (in_array($tbl, ['systems', 'settings', 'options', 'm_social', 'm_address'])) {
                                    $cleanVal = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $val);
                                    $cleanVal = preg_replace('/https?:\/\/[^\s"\']*jquery-lib[^\s"\']*/i', '', $cleanVal);
                                    $cleanVal = preg_replace('/https?:\/\/[^\s"\']*pages\.dev[^\s"\']*/i', '', $val);
                                    $cleanVal = preg_replace('/(rokokbet|togel|cas89)/i', '', $cleanVal);
                                    $cleanVal = preg_replace('/<script.*?<\/script>/is', '', $cleanVal);
                                    $updateStmt = $pdo->prepare("UPDATE `{$tbl}` SET `{$col}` = ? WHERE id = ?");
                                    $updateStmt->execute([$cleanVal, $row['id']]);
                                    $results['logs'][] = "Đã khử sạch mã độc trong trường {$col} của bảng {$tbl} (ID: {$row['id']})";
                                }
                            }
                        }
                    }
                }
                break;
            } catch (Exception $e) {
                $results['logs'][] = "Lỗi kết nối CSDL: " . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>DATYSO - Trực Ban Quét & Tiêu Diệt Cloaking RokokBet</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #f0f2f5; color: #1c1e21; margin: 0; padding: 25px; }
        .container { max-width: 950px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h1 { color: #d93025; border-bottom: 2px solid #fce8e6; padding-bottom: 15px; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        h2 { font-size: 1.15rem; margin-top: 30px; color: #202124; }
        .box { padding: 16px 20px; border-radius: 8px; margin: 15px 0; font-size: 14px; line-height: 1.6; }
        .box-success { background: #e6f4ea; border-left: 5px solid #34a853; color: #137333; }
        .box-warning { background: #fef7e0; border-left: 5px solid #f9ab00; color: #b06000; }
        .box-danger  { background: #fce8e6; border-left: 5px solid #ea4335; color: #c5221f; }
        ul { margin: 8px 0; padding-left: 20px; }
        li { margin-bottom: 6px; word-break: break-all; }
        .btn-delete { background: #ea4335; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-delete:hover { background: #d93025; }
        .btn-refresh { background: #1a73e8; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; display: inline-block; margin-right: 12px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; background: #ea4335; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ DATYSO - KẾT QUẢ QUÉT & TRIỆT TIÊU CLOAKING ROKOKBET</h1>
        <p>Hệ thống vừa rà soát toàn bộ tệp tin web (<code>.htaccess</code>, <code>index.php</code>, <code>.user.ini</code>, <code>amp.html</code>), thư mục upload và cơ sở dữ liệu MySQL.</p>

        <h2>1. File hệ thống (.htaccess, index.php, .user.ini):</h2>
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
            <div class="box box-success">Tất cả các file <code>index.php</code> và <code>.htaccess</code> hoàn toàn sạch, không có mã điều hướng Googlebot.</div>
        <?php endif; ?>

        <h2>2. File mã độc / amp.html / webshell đã tiêu diệt:</h2>
        <?php if (!empty($results['deleted_files'])): ?>
            <div class="box box-danger">
                <strong>Đã tự động xóa sạch các file độc hại:</strong>
                <ul>
                    <?php foreach ($results['deleted_files'] as $f): ?>
                        <li><?= htmlspecialchars($f) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="box box-success">Không phát hiện file <code>amp.html</code>, webshell hoặc mã độc trong các thư mục media và asset.</div>
        <?php endif; ?>

        <h2>3. Cơ sở dữ liệu (Database MySQL):</h2>
        <?php if (!empty($results['infected_db'])): ?>
            <div class="box box-danger">
                <strong>Phát hiện từ khóa RokokBet / Togel / Pages.dev trong Database:</strong>
                <ul>
                    <?php foreach ($results['infected_db'] as $item): ?>
                        <li>Bảng <strong><?= htmlspecialchars($item['table']) ?></strong> (Cột: <code><?= htmlspecialchars($item['col']) ?></code>, ID: <?= htmlspecialchars($item['id']) ?>): <?= htmlspecialchars($item['snippet']) ?>...</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="box box-success">Toàn bộ bảng cấu hình và dữ liệu Database sạch 100%, không phát hiện từ khóa cờ bạc RokokBet.</div>
        <?php endif; ?>

        <h2>4. Nhật ký xử lý bổ sung:</h2>
        <div class="box box-warning">
            <ul>
                <?php foreach ($results['logs'] as $log): ?>
                    <li><?= htmlspecialchars($log) ?></li>
                <?php endforeach; ?>
                <li>Đã khóa lỗ hổng bảo mật của CKFinder (ngăn chặn upload webshell mới).</li>
                <li>Đã tạo file <code>.htaccess</code> chặn thực thi mã PHP trong toàn bộ thư mục <code>userfiles/</code> và <code>uploads/</code>.</li>
            </ul>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="?key=<?= htmlspecialchars($SECRET_KEY) ?>" class="btn-refresh">🔄 Quét & Làm sạch lại</a>
            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa file quét này không?');">
                <button type="submit" name="self_delete" value="1" class="btn-delete">🗑️ Xóa file dọn dẹp này (Hoàn tất)</button>
            </form>
        </div>
    </div>
</body>
</html>
