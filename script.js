async function mulaiSpam() {
    const nomor = document.getElementById('nomor').value.trim();
    const jumlah = parseInt(document.getElementById('jumlah').value);
    const btn = document.getElementById('btn');
    const progress = document.getElementById('progress');
    const bar = document.getElementById('bar');
    const status = document.getElementById('status');
    const log = document.getElementById('log');

    if (!nomor.match(/^08[0-9]{8,11}$/)) {
        alert('Nomor tidak valid');
        return;
    }

    btn.disabled = true;
    progress.classList.remove('hidden');
    log.classList.remove('hidden');
    log.innerHTML = '';
    
    let sukses = 0;
    
    for (let i = 1; i <= jumlah; i++) {
        status.innerText = `Mengirim \( {i}/ \){jumlah}...`;
        bar.style.width = `${(i / jumlah) * 100}%`;
        
        try {
            const res = await fetch('api/spam.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({nomor: nomor, layanan: 'random'})
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                sukses++;
                log.innerHTML += `<div class="text-green-400">[${i}] SUCCESS → ${data.layanan}</div>`;
            } else {
                log.innerHTML += `<div class="text-red-400">[${i}] FAIL → ${data.msg}</div>`;
            }
        } catch (e) {
            log.innerHTML += `<div class="text-red-400">[${i}] ERROR</div>`;
        }
        
        // Delay biar tidak ban IP
        await new Promise(r => setTimeout(r, 800 + Math.random() * 700));
    }
    
    status.innerText = `Selesai! Sukses: \( {sukses}/ \){jumlah}`;
    btn.disabled = false;
      }
