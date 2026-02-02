document.addEventListener('DOMContentLoaded', function () {
    var btns = document.querySelectorAll('.nav-toggle');
    btns.forEach(function(btn){
        btn.addEventListener('click', function () {
            var nav = document.querySelector('.nav-links');
            if (!nav) return;
            nav.classList.toggle('show');
        });
    });
});
