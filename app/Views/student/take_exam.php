<?= $this->extend('layout') ?><?= $this->section('content') ?><div class="card"><h1><?= esc($exam['title']) ?></h1><p>Waktu tersisa: <strong id="timer">--:--</strong></p><p id="saveStatus" class="muted">Jawaban disimpan otomatis.</p></div><form id="examForm" method="post" action="/siswa/attempt/<?= (int)$attempt['id'] ?>/submit"><?= csrf_field() ?>
<?php foreach($questions as $i=>$q): $old=$saved[$q['id']]['answer_text']??''; ?><div class="card"><h2>Soal <?= $i+1 ?></h2><p><?= nl2br(esc($q['question_text'])) ?></p>
<?php if($q['question_type']==='mcq'): foreach(($q['option_order']??['A','B','C','D']) as $key): $field='option_'.strtolower($key); if(!empty($q[$field])): ?><label class="option"><input type="radio" name="q<?= (int)$q['id'] ?>" value="<?= esc($key) ?>" data-qid="<?= (int)$q['id'] ?>" <?= $old===$key?'checked':'' ?>><span><strong><?= esc($key) ?>.</strong> <?= esc($q[$field]) ?></span></label><?php endif; endforeach; else: ?><textarea class="essay" rows="4" data-qid="<?= (int)$q['id'] ?>" placeholder="Tulis jawaban..."><?= esc($old) ?></textarea><?php endif ?><span class="muted" id="saved-<?= (int)$q['id'] ?>"></span></div><?php endforeach ?>
<button class="btn green" onclick="return confirm('Kumpulkan ujian? Jawaban tidak dapat diubah setelah dikumpulkan.')">Kumpulkan Ujian</button></form>
<script>
const deadline=<?= (int)$deadline ?>*1000, tokenName=<?= json_encode(csrf_token()) ?>, attemptId=<?= (int)$attempt['id'] ?>;
let tokenHash=<?= json_encode(csrf_hash()) ?>, saveQueue=Promise.resolve(), submitting=false;
function refreshCsrf(data){
  if(!data||!data.csrfHash)return;
  tokenHash=data.csrfHash;
  document.querySelectorAll('input[name="'+tokenName+'"]').forEach(input=>input.value=tokenHash);
}
async function postForm(url, fields){
  const d=new URLSearchParams(fields);
  d.set(tokenName,tokenHash);
  const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:d.toString(),credentials:'same-origin'});
  const j=await r.json().catch(()=>({ok:false}));
  refreshCsrf(j);
  return {r,j};
}
function save(qid,value){
  saveQueue=saveQueue.then(async()=>{
    if(submitting)return;
    const {r,j}=await postForm('/siswa/attempt/'+attemptId+'/answer',{question_id:qid,answer_text:value});
    document.getElementById('saved-'+qid).textContent=j.ok?'Tersimpan '+j.saved_at:'Belum tersimpan';
    if(r.status===410&&!submitting){submitting=true;document.getElementById('examForm').requestSubmit();}
  }).catch(()=>{document.getElementById('saveStatus').textContent='Koneksi bermasalah; periksa status jawaban sebelum mengumpulkan.';});
}
document.querySelectorAll('input[type=radio][data-qid]').forEach(x=>x.addEventListener('change',()=>save(x.dataset.qid,x.value)));
document.querySelectorAll('textarea.essay').forEach(x=>{let t;x.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(()=>save(x.dataset.qid,x.value),600)});x.addEventListener('blur',()=>save(x.dataset.qid,x.value));});
setInterval(()=>{
  saveQueue=saveQueue.then(async()=>{
    if(submitting)return;
    const {j}=await postForm('/siswa/attempt/'+attemptId+'/heartbeat',{});
    if(!j.ok)document.getElementById('saveStatus').textContent='Koneksi perlu diperiksa.';
  }).catch(()=>{});
},30000);
document.getElementById('examForm').addEventListener('submit',()=>{submitting=true;});
function tick(){const left=Math.max(0,Math.floor((deadline-Date.now())/1000));document.getElementById('timer').textContent=String(Math.floor(left/60)).padStart(2,'0')+':'+String(left%60).padStart(2,'0');if(left===0&&!submitting){submitting=true;document.getElementById('examForm').requestSubmit();}}
tick();setInterval(tick,1000);
</script><?= $this->endSection() ?>