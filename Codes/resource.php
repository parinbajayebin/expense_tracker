<?php
session_start();
include 'conn.php';

$query = "SELECT * FROM financial_resources ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Resources</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .container { max-width: 800px; margin: auto; }
        .list { margin-bottom: 20px; }
        .list h2 { border-bottom: 2px solid #444; padding-bottom: 5px; }
        .list ul { list-style: none; padding: 0; }
        .list li { padding: 8px 0; }
        .list a { text-decoration: none; color: #007BFF; font-weight: bold; }
        .list a:hover { text-decoration: underline; }
        .content-box { border: 1px solid #ddd; padding: 15px; display: none; margin-top: 20px; }
    </style>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
    <div class="btn w-20 flex">
    <button onclick="goBack()" class="flex justify-end w-20">Back</button>
    </div>

    <script>
        function showResource(title, content, type, link) {
            document.getElementById('resourceTitle').innerText = title;
            document.getElementById('resourceContent').innerText = content;
            let resourceDisplay = document.getElementById('resourceDisplay');

            if (type === 'video') {
                resourceDisplay.innerHTML = `<iframe width="100%" height="315" src="${link}" frameborder="0" allowfullscreen></iframe>`;
            } else {
                resourceDisplay.innerHTML = `<p><a href="${link}" target="_blank">Read full article here</a></p>`;
            }

            document.getElementById('resourceBox').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Financial Resources</h1>

        <div class="list">
            <h2>Articles</h2>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if ($row['resource_type'] === 'article'): ?>
                        <li>
                            <a href="javascript:void(0);" onclick="showResource('<?= htmlspecialchars($row['title']) ?>', '<?= htmlspecialchars($row['content']) ?>', 'article', '<?= htmlspecialchars($row['link']) ?>')">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endwhile; ?>
            </ul>
        </div>

        <?php 
        $result->data_seek(0); 
        ?>

        <div class="list">
            <h2>Videos</h2>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if ($row['resource_type'] === 'video'): ?>
                        <li>
                            <a href="javascript:void(0);" onclick="showResource('<?= htmlspecialchars($row['title']) ?>', '<?= htmlspecialchars($row['content']) ?>', 'video', '<?= htmlspecialchars($row['link']) ?>')">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endwhile; ?>
            </ul>
        </div>

        <div id="resourceBox" class="content-box">
            <h2 id="resourceTitle"></h2>
            <p id="resourceContent"></p>
            <div id="resourceDisplay"></div>
        </div>
    </div>
</body>
</html>
