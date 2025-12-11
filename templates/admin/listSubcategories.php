<?php include "templates/include/header.php" ?>
	<?php include "templates/admin/include/header.php" ?>
	  
            <h1>Все подкатегории</h1>
	  
	<?php if ( isset( $results['errorMessage'] ) ) { ?>
	        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
	<?php } ?>
	  
	  
	<?php if ( isset( $results['statusMessage'] ) ) { ?>
	        <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
	<?php } ?>
	  
            <table>
                <tr>
                    <th>Подкатегория</th>
                    <th>Категория</th>
                </tr>

        <?php foreach ( $results['subcategories'] as $subcategory ) { ?>

                <tr onclick="location='admin.php?action=editSubcategory&amp;subcategoryId=<?php echo $subcategory->id?>'">
                    <td>
                        <?php echo htmlspecialchars($subcategory->name)?>
                    </td>
                    <td>
                        <?php echo isset($results['categories'][$subcategory->categoryId]) ? htmlspecialchars($results['categories'][$subcategory->categoryId]->name) : 'Неизвестная категория' ?>
                    </td>
                </tr>

        <?php } ?>

            </table>

            <p><?php echo $results['totalRows']?> подкатегори<?php echo ( $results['totalRows'] != 1 && $results['totalRows'] < 5 ) ? 'и' : 'й' ?> всего.</p>

            <p><a href="admin.php?action=newSubcategory">Добавить новую подкатегорию</a></p>
	  
	<?php include "templates/include/footer.php" ?>

