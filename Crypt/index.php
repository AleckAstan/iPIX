<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    include 'AES.php';

    $aes = new Crypt_AES();

    $aes->setKey('aleck');

    $size = 10;
    $plaintext = '';
    for ($i = 0; $i < $size; $i++) {
        $plaintext .= 'a';
    }

    echo $encrypted = $aes->encrypt($plaintext);

// var_dump($encrypted);
?>
    <h1>decrypted</h1>

    <?php
    $aes->setKey('test');
    echo $aes->decrypt($encrypted);
    ?>
</body>

</html>
