<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= esc($title??'CBT MIN 1 Tanggamus') ?></title>
<style>
:root{--navy:#123047;--blue:#1769aa;--green:#16856a;--bg:#f2f6fa;--text:#172b4d}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font:15px/1.5 system-ui,-apple-system,Segoe UI,sans-serif;color:var(--text)}
header{background:var(--navy);color:#fff;padding:14px 22px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}header a{color:#fff;text-decoration:none;margin-right:10px}
.container{max-width:1150px;margin:24px auto;padding:0 16px}.card{background:#fff;padding:20px;border-radius:14px;box-shadow:0 5px 20px #102a4310;margin-bottom:16px}
h1{font-size:25px;margin:0 0 14px}h2{font-size:19px;margin-top:0}input,textarea,select{font:inherit;width:100%;padding:10px;border:1px solid #ccd6e0;border-radius:8px;margin:5px 0 12px}
label{font-weight:600}.btn{display:inline-block;border:0;background:var(--blue);color:white;padding:10px 14px;border-radius:8px;text-decoration:none;cursor:pointer;font:inherit}.green{background:var(--green)}.secondary{background:#52667a}
table{width:100%;border-collapse:collapse}th,td{text-align:left;border-bottom:1px solid #e5eaf0;padding:10px;vertical-align:top}.notice{padding:12px;border-radius:8px;margin-bottom:14px;background:#eaf4ff}.error{background:#ffeded;color:#8b1e1e}.success{background:#e8f8ef;color:#17633c}.muted{color:#60758a}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px}
.option{display:flex;gap:10px;align-items:flex-start;padding:9px;border:1px solid #e0e7ef;border-radius:8px;margin:8px 0}.option input{width:auto;margin:5px 0}
@media(max-width:650px){header{padding:12px}.container{margin:14px auto}.card{padding:15px}table{display:block;overflow-x:auto}}
</style></head><body><header><strong>💻 CBT MIN 1 Tanggamus</strong><nav>
<?php if(session()->get('user_id')): ?><a href="/">Dashboard</a>
<?php if(in_array(session()->get('role'),['admin','guru'],true)): ?><a href="/guru/exams">Ujian</a><a href="/guru/monitoring">Monitoring</a><?php endif ?>
<?php if(session()->get('role')==='admin'): ?><a href="/admin/users">Pengguna</a><a href="/admin/classes">Kelas</a><a href="/admin/subjects">Mapel</a><?php endif ?>
<?php if(session()->get('role')==='siswa'): ?><a href="/siswa/exams">Ujian Saya</a><?php endif ?>
<form style="display:inline" method="post" action="/logout"><?= csrf_field() ?><button class="btn secondary">Keluar</button></form><?php endif ?>
</nav></header><main class="container">
<?php if($msg=session()->getFlashdata('error')): ?><div class="notice error"><?= esc($msg) ?></div><?php endif ?>
<?php if($msg=session()->getFlashdata('success')): ?><div class="notice success"><?= esc($msg) ?></div><?php endif ?>
<?= $this->renderSection('content') ?>
</main></body></html>