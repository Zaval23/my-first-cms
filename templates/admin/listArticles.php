<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>
	  
    <h1>All Articles</h1>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>


    <?php if ( isset( $results['statusMessage'] ) ) { ?>
            <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
    <?php } ?>

          <table>
            <tr>
              <th>Publication Date</th>
              <th>Article</th>
              <th>Category</th>
              <th>Authors</th>
              <th>Visibility</th>
            </tr>

<!--<?php echo "<pre>"; print_r ($results['articles'][2]->publicationDate); echo "</pre>"; ?> Обращаемся к дате массива $results. Дата = 0 -->
            
    <?php foreach ( $results['articles'] as $article ) { ?>

            <tr onclick="location='admin.php?action=editArticle&amp;articleId=<?php echo $article->id?>'">
              <td><?php echo date('j M Y', $article->publicationDate)?></td>
              <td>
                <?php echo $article->title?>
              </td>
              <td>
                  
             <!--   <?php echo $results['categories'][$article->categoryId]->name?> Эта строка была скопирована с сайта-->
             <!-- <?php echo "<pre>"; print_r ($article); echo "</pre>"; ?> Здесь объект $article содержит в себе только ID категории. А надо по ID достать название категории-->
            <!--<?php echo "<pre>"; print_r ($results); echo "</pre>"; ?> Здесь есть доступ к полному объекту $results -->
             
                <?php 
                if(isset ($article->categoryId)) {
                    echo $results['categories'][$article->categoryId]->name;                        
                }
                else {
                echo "Без категории";
                }?>
              </td>
              <td>
                <?php 
                if (isset($article->authors) && !empty($article->authors)) {
                    $authorNames = array();
                    foreach ($article->authors as $author) {
                        $authorNames[] = htmlspecialchars($author->login);
                    }
                    echo implode(', ', $authorNames);
                } else {
                    echo '<em>Нет авторов</em>';
                }
                ?>
              </td>
              <td onclick="event.stopPropagation()" style="text-align: center;">
    <form method="post" action="admin.php?action=updateArticleVisibility" style="display: inline; margin: 0; padding: 0;">
        <input type="hidden" name="articleId" value="<?php echo $article->id?>">
        <input type="checkbox" name="is_visible" value="1" 
            <?php echo $article->is_visible ? 'checked' : ''?>
            onchange="this.form.submit()" 
            style="width: auto; display: inline; margin: 0; transform: scale(1.2);">
    </form>
</td>
        </td>
            </tr>

    <?php } ?>

          </table>

          <p><?php echo $results['totalRows']?> article<?php echo ( $results['totalRows'] != 1 ) ? 's' : '' ?> in total.</p>

          <p><a href="admin.php?action=newArticle">Add a New Article</a></p>

<?php include "templates/include/footer.php" ?>
<script>
function updateVisibility(articleId, value) {
    // Создаем форму для отправки данных
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'admin.php?action=updateArticleVisibility';
    
    // Добавляем скрытые поля
    var articleIdField = document.createElement('input');
    articleIdField.type = 'hidden';
    articleIdField.name = 'articleId';
    articleIdField.value = articleId;
    form.appendChild(articleIdField);
    
    var visibilityField = document.createElement('input');
    visibilityField.type = 'hidden';
    visibilityField.name = 'is_visible';
    visibilityField.value = value;
    form.appendChild(visibilityField);
    
    // Добавляем форму на страницу и отправляем
    document.body.appendChild(form);
    form.submit();
}
</script>            