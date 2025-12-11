<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>
<!--        <?php echo "<pre>";
            print_r($results);
            print_r($data);
        echo "<pre>"; ?> Данные о массиве $results и типе формы передаются корректно-->

        <h1><?php echo $results['pageTitle']?></h1>

        <form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
            <input type="hidden" name="articleId" value="<?php echo $results['article']->id ?>">

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>
    
    <?php if ( isset( $results['errors'] ) && is_array( $results['errors'] ) && !empty( $results['errors'] ) ) { ?>
        <?php foreach ( $results['errors'] as $error ) { ?>
            <div class="errorMessage"><?php echo htmlspecialchars( $error ) ?></div>
        <?php } ?>
    <?php } ?>

            <ul>

              <li>
                <label for="title">Article Title</label>
                <input type="text" name="title" id="title" placeholder="Name of the article" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['article']->title )?>" />
              </li>

              <li>
                <label for="summary">Article Summary</label>
                <textarea name="summary" id="summary" placeholder="Brief description of the article" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars( $results['article']->summary )?></textarea>
              </li>

              <li>
                <label for="content">Article Content</label>
                <textarea name="content" id="content" placeholder="The HTML content of the article" required maxlength="100000" style="height: 30em;"><?php echo htmlspecialchars( $results['article']->content )?></textarea>
              </li>

              <li>
                <label for="categoryId">Article Category</label>
                <select name="categoryId" id="categoryId" onchange="updateSubcategories()">
                  <option value="0"<?php echo !$results['article']->categoryId ? " selected" : ""?>>(none)</option>
                <?php foreach ( $results['categories'] as $category ) { ?>
                  <option value="<?php echo $category->id?>"<?php echo ( $category->id == $results['article']->categoryId ) ? " selected" : ""?>><?php echo htmlspecialchars( $category->name )?></option>
                <?php } ?>
                </select>
              </li>

              <li>
                <label for="subcategoryId">Article Subcategory</label>
                <select name="subcategoryId" id="subcategoryId">
                  <option value="">(none)</option>
                  <?php 
                  if ( isset( $results['subcategoriesByCategory'] ) ) {
                      $currentCategoryId = $results['article']->categoryId ?? null;
                      foreach ( $results['subcategoriesByCategory'] as $catId => $subcategories ) { 
                          foreach ( $subcategories as $subcategory ) { 
                  ?>
                          <option value="<?php echo $subcategory->id?>" 
                                  data-category-id="<?php echo $subcategory->categoryId ?>"
                                  data-category-name="<?php echo htmlspecialchars( $results['categories'][$catId]->name ?? 'Unknown' ) ?>"
                                  <?php echo ( isset($results['article']->subcategoryId) && $subcategory->id == $results['article']->subcategoryId ) ? " selected" : "" ?>
                                  class="subcategory-option category-<?php echo $catId ?>"><?php echo htmlspecialchars( $results['categories'][$catId]->name ?? 'Unknown' ) ?> - <?php echo htmlspecialchars( $subcategory->name )?></option>
                  <?php 
                          }
                      }
                  }
                  ?>
                </select>
              </li>

              <li>
                <label for="publicationDate">Publication Date</label>
                <input type="date" name="publicationDate" id="publicationDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo $results['article']->publicationDate ? date( "Y-m-d", $results['article']->publicationDate ) : "" ?>" />
              </li>

              <li>
                <label for="authorIds">Авторы статьи</label>
                <select name="authorIds[]" id="authorIds" multiple size="5" style="min-height: 100px;">
                  <?php if (isset($results['users']) && is_array($results['users'])) { ?>
                    <?php 
                    $selectedAuthorIds = array();
                    if (isset($results['article']->authors) && is_array($results['article']->authors)) {
                        foreach ($results['article']->authors as $author) {
                            $selectedAuthorIds[] = $author->id;
                        }
                    }
                    ?>
                    <?php foreach ($results['users'] as $user) { ?>
                      <option value="<?php echo $user->id?>"<?php echo in_array($user->id, $selectedAuthorIds) ? " selected" : ""?>><?php echo htmlspecialchars($user->login)?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
                <small style="display: block; color: #666; margin-top: 5px;">Используйте Ctrl (или Cmd на Mac) для выбора нескольких авторов</small>
              </li>


            </ul>

            <div class="buttons">
              <input type="submit" name="saveChanges" value="Save Changes" />
              <input type="submit" formnovalidate name="cancel" value="Cancel" />
            </div>

        </form>

    <?php if ($results['article']->id) { ?>
          <p><a href="admin.php?action=deleteArticle&amp;articleId=<?php echo $results['article']->id ?>" onclick="return confirm('Delete This Article?')">
                  Delete This Article
              </a>
          </p>
    <?php } ?>

<script>
function updateSubcategories() {
    var categorySelect = document.getElementById('categoryId');
    var subcategorySelect = document.getElementById('subcategoryId');
    var selectedCategoryId = categorySelect.value;
    
    // Обрабатываем все опции подкатегорий
    var options = subcategorySelect.getElementsByTagName('option');
    var selectedValue = subcategorySelect.value;
    var foundSelected = false;
    
    for (var i = 0; i < options.length; i++) {
        var option = options[i];
        var optionCategoryId = option.getAttribute('data-category-id');
        
        // Пропускаем опцию "(none)"
        if (option.value === '') {
            continue;
        }
        
        // Показываем только опции выбранной категории
        if (optionCategoryId == selectedCategoryId && selectedCategoryId && selectedCategoryId != '0') {
            option.style.display = '';
            if (option.value == selectedValue) {
                foundSelected = true;
            }
        } else {
            option.style.display = 'none';
        }
    }
    
    // Если выбранная подкатегория не принадлежит выбранной категории, сбрасываем выбор
    if (!foundSelected && selectedValue) {
        subcategorySelect.value = '';
    }
    
    // Если категория не выбрана, сбрасываем подкатегорию
    if (!selectedCategoryId || selectedCategoryId == '0') {
        subcategorySelect.value = '';
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    updateSubcategories();
});
</script>
	  
<?php include "templates/include/footer.php" ?>

              