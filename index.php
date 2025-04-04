<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каныкей Бакытбекова </title>
    <style>
        .kani {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; 
        }
        .koko {
            flex: 1;
            margin-right: 20px;
        }
        .lili {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .koko-info {
            display: flex;
            align-items: flex-start; 
        }
        .koko-info img {
            width: 150px;
            height: 150px;
            margin-right: 20px;
        }
    </style>
</head>
<body>
<div class="kani">
        <div class="koko">
            <form action="" method="post" enctype="multipart/form-data">
                <p>Ваша фамилия: <input type="text" name="last_name" /></p>
                <p>Ваше имя: <input type="text" name="name" /></p>
                <p>Ваш возраст: <input type="text" name="age" /></p>
                <p>Загрузите фото: <input type="file" name="photo" /></p>
                <p>Расскажите о себе: <textarea name="about"></textarea></p>
                <p><input type="submit" value="Отправить"></p>
            </form>
        </div>

        <div class="lili">
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $lastName = htmlspecialchars($_POST['last_name'] ?? '');
                $name = htmlspecialchars($_POST['name'] ?? '');
                $age = htmlspecialchars($_POST['age'] ?? '');
                $about = htmlspecialchars($_POST['about'] ?? '');

                if (!empty($_FILES["photo"]["name"])) {
                    $uploadDir = "uploads/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = basename($_FILES["photo"]["name"]);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $uploadFile)) {
                        echo "<div class='koko-info'>";
                        echo "<img src='$uploadFile' alt='Фото пользователя'>";
                        echo "<div>";
                        echo "<p><strong>Привет, $name $lastName!</strong></p>";
                        echo "<p>Вам $age лет.</p>";
                        echo "<p>О себе: $about</p>";
                        echo "</div></div>";
                    } else {
                        echo "<p>Ошибка загрузки фото.</p>";
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>