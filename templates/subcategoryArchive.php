<?php include "templates/include/header.php" ?>
	  
    <h1><?php echo htmlspecialchars( $results['pageHeading'] ) ?></h1>
    
    <?php if ( $results['subcategory'] ) { ?>
    <p class="categoryDescription">
        Подкатегория категории 
        <a href="./?action=archive&amp;categoryId=<?php echo $results['category']->id?>">
            <?php echo htmlspecialchars( $results['category']->name ) ?>
        </a>
    </p>
    <?php } ?>

    <ul id="headlines" class="archive">

    <?php foreach ( $results['articles'] as $article ) { ?>

            <li>
                <h2>
                    <span class="pubDate">
                        <?php echo date('j F Y', $article->publicationDate)?>
                    </span>
                    <a href=".?action=viewArticle&amp;articleId=<?php echo $article->id?>">
                        <?php echo htmlspecialchars( $article->title )?>
                    </a>

                    <?php if ( $article->categoryId ) { ?>
                    <span class="category">
                        in 
                        <a href=".?action=archive&amp;categoryId=<?php echo $article->categoryId?>">
                            <?php echo htmlspecialchars( $results['categories'][$article->categoryId]->name ) ?>
                        </a>
                    </span>
                    <?php } ?>
                    
                    <?php if (isset($article->authors) && !empty($article->authors)) { ?>
                    <span class="category">
                        , автор<?php echo count($article->authors) > 1 ? 'ы' : '' ?>: 
                        <?php 
                        $authorNames = array();
                        foreach ($article->authors as $author) {
                            $authorNames[] = htmlspecialchars($author->login);
                        }
                        echo implode(', ', $authorNames);
                        ?>
                    </span>
                    <?php } ?>          
                </h2>
              <p class="summary"><?php echo htmlspecialchars( $article->summary )?></p>
            </li>

    <?php } ?>

    </ul>

    <p><?php echo $results['totalRows']?> стат<?php echo ( $results['totalRows'] % 10 >= 2 && $results['totalRows'] % 10 <= 4 && ( $results['totalRows'] % 100 < 10 || $results['totalRows'] % 100 >= 20 ) ) ? 'ьи' : ( ( $results['totalRows'] % 10 == 1 && $results['totalRows'] % 100 != 11 ) ? 'ья' : 'ей' ) ?> всего.</p>

    <p><a href="./">Вернуться на главную страницу</a></p>
	  
<?php include "templates/include/footer.php" ?>

