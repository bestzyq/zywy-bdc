<?php
session_start();

// 读取 CSV 文件并将其转换为一个关联数组
function loadWords($filename) {
    $words = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $words[] = ['word' => $data[0], 'meaning' => $data[1]];
        }
        fclose($handle);
    }
    return $words;
}

$words = loadWords('words.csv');
$words_per_page = 10;
$total_words = count($words);
$total_pages = ceil($total_words / $words_per_page);

// 获取当前页码
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($total_pages, $current_page));

// 计算当前页的开始和结束索引
$start_index = ($current_page - 1) * $words_per_page;
$end_index = min($start_index + $words_per_page, $total_words);

// 设置分页显示的页码范围
$pagination_range = 0;
$start_page = max(1, $current_page - $pagination_range);
$end_page = min($total_pages, $current_page + $pagination_range);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>单词对照表</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            max-width: 90%;
            width: 600px;
            overflow-y: auto;
            max-height: 90vh;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        button {
            padding: 5px 10px;
            font-size: 14px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            text-decoration: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination a.active {
            background-color: #0056b3;
        }
        footer {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
            color: #888;
        }
        footer img {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>单词对照表</h1>
        <h3><a href="/">去测试</a></h3>
        <table>
            <thead>
                <tr>
                    <th>单词</th>
                    <th>中文解释</th>
                    <th>发音</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = $start_index; $i < $end_index; $i++): ?>
                    <tr>
                        <td><a href="https://www.iciba.com/word?w=<?php echo htmlspecialchars($words[$i]['word'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($words[$i]['word'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                        <td><?php echo htmlspecialchars($words[$i]['meaning'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><button onclick="playAudio('<?php echo htmlspecialchars($words[$i]['word'], ENT_QUOTES, 'UTF-8'); ?>')">播放</button></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=1">首页</a>
                <a href="?page=<?php echo $current_page - 1; ?>">&laquo;</a>
            <?php endif; ?>
            <?php for ($page = $start_page; $page <= $end_page; $page++): ?>
                <a href="?page=<?php echo $page; ?>" class="<?php echo $page == $current_page ? 'active' : ''; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>">&raquo;</a>
                <a href="?page=<?php echo $total_pages; ?>">末页</a>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <a href="https://beian.miit.gov.cn/" target="_blank">苏ICP备2023000758号-3</a>
        <br/>
        <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31012102000146">
            <img src="https://beian.mps.gov.cn/img/ghs.png" alt="沪公网安备">
            沪公网安备31012102000146号
        </a>
        <br/>
        <span id="ipv4"></span><span id="ipv6"></span>
    </footer>
    <script src="https://net.sjtu.edu.cn/script/jquery.min.js"></script>
    <script src="https://net.sjtu.edu.cn/script/nav.js"></script>
    <script>
        function playAudio(word) {
            var audio = new Audio('https://dict.youdao.com/dictvoice?type=1&audio=' + encodeURIComponent(word));
            audio.play();
        }
    </script>
</body>
</html>
