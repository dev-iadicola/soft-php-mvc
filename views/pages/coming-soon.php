<style>
    .lottie-container {
        display: flex;
        justify-content: center;
        /* orizzontale */
        align-items: center;
        /* verticale */
        height: 100vh;
    
        /* opzionale */
    }

    #anim {
        width: 80%;
        /* dimensione */
      
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.0/lottie.min.js"></script>

 <h1>COMING SOON</h1>
    <h3>stiamo migliorando il servizio, visitaci più tardi!</h3>
<div class="lottie-container">
   
  <div id="anim"></div>
</div>
<script>
    lottie.loadAnimation({
        container: document.getElementById('anim'),
        renderer: 'svg', // svg è il più leggero e pulito
        loop: true,
        autoplay: true,
        path: '<?= assets('lottie/coming-soon.json') ?>' // percorso relativo al file scaricato
    });
</script>