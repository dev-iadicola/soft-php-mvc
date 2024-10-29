<style>
        .container {
            padding: 15px;
        }

        .card-body {
            padding: 20px;
        }

        .shadow-xl {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Media queries for responsive adjustments */
        @media (max-width: 576px) {
            .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .gap-5 {
                gap: 10px;
            }

            .stretched-link {
                font-size: 16px;
            }

            .card-body h2 {
                font-size: 18px;
            }

            hr {
                width: 100%;
            }

            .col-md-12 {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-start gap-5 flex-row flex-wrap mb-4">
            <?php foreach ($laws as $law) : ?>
                <a href="#<?= $law->id ?>" class="stretched-link mr-5"><?= htmlspecialchars($law->title, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endforeach ?>
        </div>

        <div class="d-flex flex-column flex-wrap">
            <?php foreach ($laws as $law) : ?>
                <div class="col-12 mb-4" id="<?= $law->id ?>">
                    <div class="shadow-xl rounded bg-white" style="min-height: 900px; width: 100%; max-width: 1000px;">
                        <!-- Card Body -->
                        <div class="card-body">
                            <h2 class=""><?= htmlspecialchars($law->title, ENT_QUOTES, 'UTF-8') ?></h2>
                            <div>
                                <?= $law->testo ?>
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>
                </div>
            <?php endforeach; ?>
            <hr style="color:red; width:100%; height:10px;">
        </div>
    </div>
