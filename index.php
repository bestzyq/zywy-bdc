<?php
session_start();

// 检查并初始化用户会话
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_', true);
}

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

// 随机选择一个词组
function getRandomWord($words) {
    return $words[array_rand($words)];
}

$words = loadWords('words.csv');

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = trim($_POST['answer']);
    $correct_answer = $_SESSION[$_SESSION['user_id']]['current_word']['word'];
    $correct_meaning = $_SESSION[$_SESSION['user_id']]['current_word']['meaning'];
    if ($answer === $correct_answer) {
        $message = "正确！";
    } else {
        $message = "错误，“" . $correct_meaning . "”的正确答案是: " . $correct_answer;
    }
    unset($_SESSION[$_SESSION['user_id']]['current_word']);
} else {
    $message = "";
}

if (!isset($_SESSION[$_SESSION['user_id']]['current_word'])) {
    $_SESSION[$_SESSION['user_id']]['current_word'] = getRandomWord($words);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>背单词</title>
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
            width: 300px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            font-size: 16px;
            color: #ff0000;
        }
        .audio-button {
            background-color: #28a745;
        }
        .audio-button:hover {
            background-color: #218838;
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
        <h1>背单词</h1>
        <p><?php echo $_SESSION[$_SESSION['user_id']]['current_word']['meaning']; ?></p>
        <form method="post" id="wordForm">
            <input type="text" name="answer" id="answer" placeholder="输入英文单词" required>
            <button type="submit" onclick="setFocus()">提交</button>
        </form>
        <button class="audio-button" onclick="playAudio()">听发音</button>
        <p class="message"><?php echo $message; ?></p>
    </div>
    <footer>
        <a href="https://beian.miit.gov.cn/" target="_blank">苏ICP备2023000758号-3</a><br/>
        <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31012102000146">
            <img src="https://beian.mps.gov.cn/img/ghs.png" alt="沪公网安备">
            沪公网安备31012102000146号
        </a>
        <br/>
        <span>适用于华东理工大学能源与动力工程专业英语</span><br/>
        <span id="ipv4"></span><span id="ipv6"></span>
    </footer>
    <script src="https://net.sjtu.edu.cn/script/jquery.min.js"></script>
    <script src="https://net.sjtu.edu.cn/script/nav.js"></script>
    <script>
        function playAudio() {
            var word = "<?php echo $_SESSION[$_SESSION['user_id']]['current_word']['word']; ?>";
            var audio = new Audio('https://dict.youdao.com/dictvoice?type=1&audio=' + encodeURIComponent(word));
            audio.play();
        }

        function setFocus() {
            setTimeout(function() {
                document.getElementById('answer').focus();
            }, 100);
        }

        // 在页面加载时自动聚焦输入框
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('answer').focus();
        });
    </script>
</body>
</html>
