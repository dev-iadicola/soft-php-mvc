<style>
    .arrow-icon {
        display: inline-block;
        opacity: 1;
        transform: translateX(0);
        transition:
            opacity 0.3s ease,
            transform 0.3s ease,
            width 0.3s ease,
            margin 0.3s ease;
        width: auto;
        margin-right: 8px;
        /* o quello che preferisci */
    }
    .text-block *{
        color: black;
    }
    .arrow-icon.hidden-arrow {
        opacity: 0;
        transform: translateX(-10px);
        width: 0;
        margin-right: 0;
        overflow: hidden;
    }
</style>
<div class="card shadow-sm border-light rounded-lg bg-white overflow-hidden mb-4">
  <div class="row g-0 align-items-center">
    
    <!-- Immagine -->
    <div class="col-md-4">
      <img 
        src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>" 
        class="img-fluid rounded-start object-fit-contain p-3" 
        alt="<?= $project->title ?>"
      />
    </div>

    <!-- Contenuto -->
    <div class="col-md-8 p-4">
      <h1 class="fw-bold text-dark mb-2"><?= $project->title ?></h1>

      <h5 class="fw-semibold mb-2">Descrizione</h5>
      <article class="text-start text-secondary mb-4 lh-base text-block">
        <?= $project->overview ?>
      </article>

      <div class="d-flex flex-wrap gap-3">
        <?php if (!isset($project->link)): ?>
          <a
          onmouseover="showArrow('<?= $project->link ?>')" onmouseleave="hideArrow('<?= $project->link ?>')" 
          href="<?= $project->link ?>"
             target="_blank"
             rel="noopener noreferrer"
             class="btn btn-dark text-white d-flex align-items-center gap-2 px-3">
            <i class="fa fa-github"></i> Code
          </a>
        <?php endif; ?>

        <?php if (urlExist($project->website)): ?>
          <a href="<?= $project->website ?>"
             target="_blank"
             rel="noopener noreferrer"
             class="btn btn-primary d-flex align-items-center gap-2 px-3">
            <i class="fa fa-eye"></i> Website
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


@include('pages.progetti')






<script>
    function showArrow(id) {
        const arrow = document.getElementById(id);
        if (arrow) {
            arrow.classList.remove('hidden-arrow');
        }
    }

    function hideArrow(id) {
        const arrow = document.getElementById(id);
        if (arrow) {
            arrow.classList.add('hidden-arrow');
        }
    }
</script>