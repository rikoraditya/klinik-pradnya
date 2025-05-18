//ambil elemen
var keyword = document.getElementById('keyword');
var tombolCari = document.getElementById('tombol-cari');
var container = document.getElementById('container');

//tambah event ketika keyword di ketik
keyword.addEventListener('keyup', function() {
var xhr = new XMLHttpRequest();

//cek ajax
xhr.onreadystatechange = function() {
    if( xhr.readyState == 4 && xhr.status == 200) {
       container.innerHTML = xhr.responseText;
    }
}

//eksekusi ajax
xhr.open('GET', '../../js/ajax/pasien.php?keyword=' + keyword.value, true);
xhr.send();



});


