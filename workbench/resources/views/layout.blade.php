<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testovací PDF Layout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .note {
            font-size: 0.9em;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Fiktívna Faktúra</h1>
    <p>Dátum: 4. júl 2025</p>
</header>

<section class="info">
    <p><strong>Dodávateľ:</strong> Spoločnosť ABC s.r.o.</p>
    <p><strong>Odberateľ:</strong> Testovacia Firma s.r.o.</p>
</section>

<table>
    <thead>
        <tr>
            <th>Položka</th>
            <th>Popis</th>
            <th>Množstvo</th>
            <th>Cena za ks</th>
            <th>Celkom</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>001</td>
            <td>Testovací produkt A</td>
            <td>3</td>
            <td>25 €</td>
            <td>75 €</td>
        </tr>
        <tr>
            <td>002</td>
            <td>Služba B</td>
            <td>2</td>
            <td>50 €</td>
            <td>100 €</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;"><strong>Spolu</strong></td>
            <td><strong>175 €</strong></td>
        </tr>
    </tbody>
</table>

<div class="note">
    Poznámka: Toto je fiktívny dokument vygenerovaný na testovacie účely.
</div>

</body>
</html>
