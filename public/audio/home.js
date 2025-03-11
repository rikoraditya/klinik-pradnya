let audio;

window.onload = function () {
    if (!window.audioInstance) {
        audio = new Audio('audio/audio.mp3'); // Ganti dengan URL audio kamu
        audio.loop = false;
        audio.volume = 0.5;
        
        // Event pertama kali klik atau scroll akan memulai audio
        document.addEventListener("click", playAudio);
        document.addEventListener("scroll", playAudio);

        // Simpan instance audio agar tidak mati saat berpindah halaman
        window.audioInstance = audio;
    } else {
        audio = window.audioInstance;
    }
};

function playAudio() {
    if (audio.paused) {
        audio.play();
    }
}
