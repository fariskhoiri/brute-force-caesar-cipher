<?php
// file: index.php
// Minimal explanation only: web form menerima ciphertext dan menampilkan brute-force Caesar 0..25.

function caesar_decrypt(string $text, int $key): string {
    $result = '';
    $n = strlen($text);
    for ($i = 0; $i < $n; $i++) {
        $c = $text[$i];
        $ord = ord($c);
        // A-Z
        if ($ord >= 65 && $ord <= 90) {
            $pos = $ord - 65;
            $newPos = ($pos - $key) % 26;
            if ($newPos < 0) $newPos += 26;
            $result .= chr(65 + $newPos);
            continue;
        }
        // a-z
        if ($ord >= 97 && $ord <= 122) {
            $pos = $ord - 97;
            $newPos = ($pos - $key) % 26;
            if ($newPos < 0) $newPos += 26;
            $result .= chr(97 + $newPos);
            continue;
        }
        // non-letter: keep as-is
        $result .= $c;
    }
    return $result;
}

$input = '';
$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = isset($_POST['ciphertext']) ? (string)$_POST['ciphertext'] : '';
    // keep original but sanitize for output
    $safe_input = htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    // compute all shifts 0..25
    for ($k = 0; $k < 26; $k++) {
        $dec = caesar_decrypt($input, $k);
        $results[] = ['key' => $k, 'text' => $dec];
    }
} else {
    $safe_input = '';
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Brute-force Caesar Cipher — PHP</title>
<style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;margin:28px;background:#f7f7f9;color:#111}
    .card{max-width:900px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
    textarea{width:100%;min-height:100px;padding:10px;font-size:16px;border:1px solid #d7d7df;border-radius:6px}
    table{width:100%;border-collapse:collapse;margin-top:14px}
    th,td{padding:8px 10px;border-bottom:1px solid #eee;text-align:left;font-family:monospace}
    th{background:#fafafa}
    .btn{display:inline-block;padding:8px 12px;border-radius:6px;border:0;background:#111;color:#fff;cursor:pointer}
    .small{font-size:13px;color:#666;margin-top:8px}
    .copy-btn{padding:6px 8px;font-size:13px;border-radius:6px;border:0;background:#2b7cff;color:#fff;cursor:pointer}
    .key-badge{display:inline-block;padding:4px 8px;border-radius:6px;background:#f0f0f4;font-weight:600;margin-right:8px}
</style>
</head>
<body>
<div class="card">
    <h2>Brute-force Caesar Cipher (PHP)</h2>
    <p class="small">Masukkan teks sandi Caesar (alfabet A-Z / a-z). Program akan mencoba semua kunci 0–25 dan menampilkan hasil dekripsi.</p>

    <form method="post" novalidate>
        <label for="ciphertext"><strong>Ciphertext</strong></label><br />
        <textarea id="ciphertext" name="ciphertext" placeholder="Masukkan teks di sini..."><?= $safe_input ?></textarea>
        <div style="margin-top:10px">
            <button class="btn" type="submit">Brute-force</button>
            <button class="copy-btn" type="button" onclick="document.getElementById('ciphertext').value = '';">Bersihkan</button>
        </div>
    </form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <h3 style="margin-top:18px">Hasil (<?= htmlspecialchars(strlen($input), ENT_QUOTES) ?> karakter)</h3>
    <table>
        <thead>
            <tr><th style="width:80px">Kunci</th><th>Hasil Dekripsi</th><th style="width:120px">Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr id="row-<?= $row['key'] ?>">
                <td><span class="key-badge"><?= $row['key'] ?></span></td>
                <td><pre style="margin:0;white-space:pre-wrap;"><?= htmlspecialchars($row['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre></td>
                <td>
                    <button class="copy-btn" onclick="copyText(<?= $row['key'] ?>)">Copy</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="small">Catatan: Periksa hasil yang paling bermakna (konteks/grammar). Caesar cipher hanya menggeser alfabet Latin A–Z.</p>
<?php endif; ?>

</div>

<script>
function copyText(key) {
    const row = document.getElementById('row-' + key);
    if (!row) return;
    const pre = row.querySelector('pre');
    const text = pre ? pre.innerText : '';
    navigator.clipboard?.writeText(text).then(function(){
        alert('Teks hasil (kunci ' + key + ') disalin ke clipboard.');
    }).catch(function(){
        // fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); alert('Teks disalin ke clipboard.'); }
        catch(e){ alert('Salin gagal.'); }
        document.body.removeChild(ta);
    });
}
</script>
</body>
</html>
