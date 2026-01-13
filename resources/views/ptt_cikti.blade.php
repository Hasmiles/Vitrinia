<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Türkçe karakterler için şart */
            font-size: 12px;
        }
        .container {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
            padding: 5px;
        }
        .header {
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .barcode-area {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">PTT ALMA HABER KARTI</div>
        
        <table>
            <tr>
                <td width="50%" style="border-right: 1px solid #ccc;">
                    <strong>GÖNDERİCİ:</strong><br>
                    {{ $data['gonderici'] }}<br>
                    İletişim: {{ $data['phone'] }}
                </td>
                <td width="50%">
                    <strong>ALICI:</strong><br>
                    {{ $data['alici'] }}<br>
                    {{ $data['adres'] }}
                </td>
            </tr>
        </table>

        <div class="barcode-area">
            <img src="data:image/png;base64,{{ $barcode }}" height="50">
            <br>
            <span>{{ $data['barkod_no'] }}</span>
        </div>
    </div>
</body>
</html>