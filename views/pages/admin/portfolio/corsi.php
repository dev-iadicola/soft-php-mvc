 <!-- Admin Panel -->
 <div class="container admin-panel">
     <h1 class="my-4">Course Management  <?= isset($element->title)?></h1>

     <!-- Form to Add portfolio -->

     <div class="mb-4">
         <?php
            $url = isset($element->id) ? "/admin/corso-update/{$element->id}" : '/admin/corsi';

            ?>

         <form method="POST" /admin/portfolio-update/{id} action="<?= $url ?>">
             <div class="form-group">
                 <label for="title">Title</label>
                 <input type="text" placeholder="Title of certificate" 
                 class="form-control" name="title" value="<?= isset($element->title) ? $element->title : '' ?>" required>
             </div>

             <div class="form-group">
                 <label for="title">Institution</label>
                 <input type="text" placeholder="Institution that issued the certificate" 
                  class="form-control" name="ente" value="<?= isset($element->ente) ? $element->ente : '' ?>" required>
             </div>

             <div class="form-group">
                 <label for="overview">Overview</label>
                 <textarea class="form-control"   name="overview" rows="3"><?= isset($element->overview) ? $element->overview : '' ?></textarea>
             </div>
             <div class="form-group">
                 <label for="link">Link</label>
                 <input type="url" name="link" class="form-control" placeholder="https://example.com" id="link" 
                     value="<?= isset($element->link) ? $element->link: '' ?>" requered>
             </div>

             <div class="form-group">
                 <label for="link">Date Certified</label>
                 <input type="number" min="1900" max="2099" step="1"  class="form-control" id="date" name="certified" requered 
                 value="<?= isset($element->certified) ? $element->certified : date('Y') ?>">
             </div>
             <button type="submit" class="btn btn-primary">Submit</button>
         </form>

     </div>

     <!-- portfolio Table -->
     <div>
         <h3>Certified Exists</h3>
         <table class="table table-bordered">
             <thead>
                 <tr>
                     <th>ID</th>
                     <th>Title</th>
                     <th>Overview</th>
                     <th>Link</th>
                     <th>Date</th>
                     <th>Actions</th>
                 </tr>
             </thead>
             <tbody id="projectsTableBody">
                 <?php foreach ($corsi as $corso) : ?>
                     <tr>
                         <td><?= $corso->id ?></td>

                         <td><?= $corso->title ?></td>
                         <td><?= $corso->overview ?></td>
                         <td>
                             <?php if ($corso->link !== '') : ?>
                                 <a href="<?= $corso->link ?>" target="_blank" class="btn btn-primary">
                                     Apri il link per <?= $corso->title ?>
                                 </a>
                             <?php endif ?>

                         </td>
                        
                         <td>
                            <?= $corso->certified ?>
                         </td>
                         <td class="col-ms-3 p-2 gap-3">
                             <form action="/admin/corso-delete/<?= $corso->id ?>" method="POST">
                                 @delete
                                 <button onclick="return confirm('Are you sure you want to eliminate <?= $corso->title ?>')" class="btn btn-danger">Delete</button>
                             </form>
                             <a href="/admin/corso-edit/<?= $corso->id ?>" class="btn btn-warning my-3">Edit</a>
                         </td>
                     </tr>
                 <?php endforeach ?>
             </tbody>
         </table>
     </div>
 </div>