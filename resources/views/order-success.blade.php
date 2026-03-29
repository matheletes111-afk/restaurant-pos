<!DOCTYPE html>
<html>
<head>
    <title>Order Placed Successfully</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background: #faf7e8;
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 25px;
            text-align: center;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .icon {
            font-size: 80px;
            color: #22c55e;
            margin-bottom: 10px;
            animation: pop 0.5s ease-out;
        }

        @keyframes pop {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #111;
        }

        .subtitle {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }

        .btn-home {
            margin-top: 25px;
            display: inline-block;
            padding: 12px 25px;
            background: #ff9800;
            color: white;
            font-size: 16px;
            border-radius: 12px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-50 to-green-100 p-6">

    <div class="success-card shadow-2xl rounded-3xl bg-white p-10 text-center border border-green-200">

        <div class="icon mx-auto text-6xl mb-4">😊</div>

        <div class="title text-3xl font-bold text-green-700">
            Order Received!
        </div>

        <div class="subtitle text-gray-600 mt-2 text-lg">
            Please wait… your order is <span class="font-semibold text-green-800">waiting for approval</span>.
            <br>Our team will confirm it shortly.
        </div>

        <!-- <a href="/" 
           class="btn-home inline-block mt-8 bg-green-600 hover:bg-green-700 text-white py-3 px-8 rounded-full text-lg font-semibold transition shadow-lg">
            Back to Home
        </a> -->
    </div>

</div>

<style>
.success-card {
    max-width: 420px;
    animation: pop 0.4s ease-out;
}
@keyframes pop {
    0%   { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>


</body>
</html>
