<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Sign In</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --cel-red: #d8232a;
            --dark-bg: #1a1a1a;
            --text-gray: #666666;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            display: flex;
            width: 900px;
            max-width: 95%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            min-height: 550px;
        }

        .auth-branding {
            flex: 1;
            background: var(--dark-bg);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .auth-form-container {
            flex: 1.1;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cel-logo-wrapper {
            margin-bottom: 30px;
        }

        .cel-logo {
            width: 120px;
            height: 120px;
            background: var(--cel-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 42px;
            color: white;
            box-shadow: 0 0 20px rgba(216, 35, 42, 0.4);
            position: relative;
        }

        /* Stylized CEL Logo Shape */
        .cel-logo::before {
            content: 'CEL';
            font-style: italic;
        }

        .branding-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .branding-subtitle {
            font-size: 16px;
            opacity: 0.8;
            font-weight: 400;
        }

        /* Form Styles */
        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: var(--text-gray);
            font-size: 16px;
            margin-bottom: 40px;
        }

        .label-premium {
            font-size: 12px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 25px;
        }

        .input-premium {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .input-premium:focus {
            border-color: var(--cel-red);
            outline: none;
            box-shadow: 0 0 0 3px rgba(216, 35, 42, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }

        .btn-cel {
            width: 100%;
            background: var(--cel-red);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-cel:hover {
            background: #be1e24;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(216, 35, 42, 0.3);
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: var(--text-gray);
        }

        .form-footer a {
            color: var(--cel-red);
            text-decoration: none;
            font-weight: 600;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
        }

        .forgot-password {
            color: var(--cel-red);
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 850px) {
            .auth-container {
                flex-direction: column;
                width: 450px;
                min-height: auto;
            }
            .auth-branding {
                padding: 30px;
            }
            .auth-form-container {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Branding Panel -->
        <div class="auth-branding">
            <div class="cel-logo-wrapper">
                <div class="cel-logo"></div>
            </div>
            <h1 class="branding-title">Central Electronics<br>Limited</h1>
            <p class="branding-subtitle">Welcome to the Advanced Corporate Suite</p>
        </div>

        <!-- Form Panel -->
        <div class="auth-form-container">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
