<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sazanami</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Figmaで設定された独自カラー（primaryなど）をTailwindに設定
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5ba3c5',
                        'primary-foreground': '#ffffff',
                        secondary: '#e0f2fe',
                        accent: '#bae6fd',
                        foreground: '#333333',
                        'muted-foreground': '#666666',
                        border: '#e5e7eb'
                    }
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-latest.js"></script>
	<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50">

<?php $is_logged_in = \Session::get('user_id') ? true : false; ?>
<?php echo View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

</body>
</html>