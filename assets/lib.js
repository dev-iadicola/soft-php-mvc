const icon = document.getElementById('menu-icon');
const menu = document.getElementById('menu');
console.log('javascript actived');

icon.addEventListener('click', toggleMenu);

function toggleMenu(){
    icon.classList.toggle('trasforma');
    menu.classList.toggle('chiuso')
    menu.classList.toggle('aperto')

}

function downloadFunction(id) {
    $.post("/download/" + id, {
      
    }, function(data, status) {
       
    });
}


// OLD
 // fade in e fade out per effetto visivo
 $(document).ready(function () {
    const $fadeInSections = $('.fade-in-section');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                $(entry.target).addClass('is-visible');
                observer.unobserve(entry.target);
            }
        });
    });

    $fadeInSections.each(function () {
        observer.observe(this);
    });

   

   
});

// bottone per tornare all'header

$(function() {
    const btnArrow = $('#btn-arrow');
    console.log(btnArrow)
   
    if (btnArrow.length) {
        $(window).scroll(function() {
            var aTop = $('#footer').height();
            if ($(this).scrollTop() >= aTop) {
                btnArrow.addClass('visible');
                console.log(btnArrow.attr('class')); // Mostra le classi dell'elemento btnArrow nella console
            } else {
                btnArrow.removeClass('visible');
                console.log(btnArrow.attr('class')); // Mostra le classi dell'elemento btnArrow nella console
            }
        });
    } else {
        console.log('btnArrow not found');
    }
});

// toggle cards
$(document).ready(function () {
    $(".card").fadeIn(2000);
})
function toggleCards(idCards, idHide) {
    $(idCards).fadeToggle('slow');
    $(idHide).fadeOut('slow');
}