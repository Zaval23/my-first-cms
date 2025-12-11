<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

        <h1><?php echo $results['pageTitle']?></h1>

        <form action="admin.php?action=<?php echo $results['formAction']?>" method="post"> 
        <input type="hidden" name="subcategoryId" value="<?php echo $results['subcategory']->id ?? '' ?>"/>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

        <ul>

          <li>
            <label for="name">Название подкатегории</label>
            <input type="text" name="name" id="name" placeholder="Название подкатегории" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['subcategory']->name ?? '' )?>" />
          </li>

          <li>
            <label for="categoryId">Категория</label>
            <select name="categoryId" id="categoryId" required>
              <option value="">(выберите категорию)</option>
            <?php foreach ( $results['categories'] as $category ) { ?>
              <option value="<?php echo $category->id?>"<?php echo ( isset($results['subcategory']->categoryId) && $category->id == $results['subcategory']->categoryId ) ? " selected" : ""?>><?php echo htmlspecialchars( $category->name )?></option>
            <?php } ?>
            </select>
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="saveChanges" value="Сохранить изменения" />
          <input type="submit" formnovalidate name="cancel" value="Отмена" />
        </div>

      </form>

    <?php if ( isset($results['subcategory']->id) && $results['subcategory']->id ) { ?>
          <p><a href="admin.php?action=deleteSubcategory&amp;subcategoryId=<?php echo $results['subcategory']->id ?>" onclick="return confirm('Удалить эту подкатегорию?')">Удалить эту подкатегорию</a></p>
    <?php } ?>

<?php include "templates/include/footer.php" ?>


