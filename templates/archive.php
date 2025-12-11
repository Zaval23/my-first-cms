<?php include "templates/include/header.php" ?>
	  
    <h1><?php echo htmlspecialchars( $results['pageHeading'] ) ?></h1>
    
    <?php if ( $results['category'] ) { ?>
    <h3 class="categoryDescription"><?php echo htmlspecialchars( $results['category']->description ) ?></h3>
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

                    <?php if ( !$results['category'] && $article->categoryId ) { ?>
                    <span class="category">
                        in 
                        <a href=".?action=archive&amp;categoryId=<?php echo $article->categoryId?>">
                            <?php echo htmlspecialchars( $results['categories'][$article->categoryId]->name ) ?>
                        </a>
                    </span>
                    <?php } ?>
                    
                    <?php if ( isset($article->subcategoryId) && isset($results['subcategories'][$article->subcategoryId]) ) { ?>
                    <span class="category">
                        , подкатегория 
                        <a href=".?action=subcategory&amp;subcategoryId=<?php echo $article->subcategoryId?>">
                            <?php echo htmlspecialchars( $results['subcategories'][$article->subcategoryId]->name ) ?>
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

    <p><?php echo $results['totalRows']?> article<?php echo ( $results['totalRows'] != 1 ) ? 's' : '' ?> in total.</p>

    <p><a href="./">Return to Homepage</a></p>
	  
<?php include "templates/include/footer.php" ?>