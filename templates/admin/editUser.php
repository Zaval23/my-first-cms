<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

    <h1><?php echo $results['pageTitle']?></h1>

    <form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
        <input type="hidden" name="userId" value="<?php echo $results['user']->id ?? '' ?>">

        <?php if (isset($results['errorMessage'])) { ?>
                <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
        <?php } ?>

        <ul>
            <li>
                <label for="login">Логин</label>
                <input type="text" name="login" id="login" placeholder="Введите логин" required autofocus maxlength="50" value="<?php echo htmlspecialchars($results['user']->login ?? '')?>" />
            </li>

            <li>
                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" placeholder="Введите пароль" maxlength="255" <?php echo !isset($results['user']->id) ? 'required' : '' ?> />
                <?php if (isset($results['user']->id)) { ?>
                    <small style="color: #666;">Оставьте пустым, чтобы сохранить текущий пароль</small>
                <?php } ?>
            </li>

            <li style="display: flex; align-items: center; gap: 10px;">
                <label for="is_active">Активен</label>
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       <?php echo ($results['user']->is_active ?? 1) ? 'checked="checked"' : '' ?> 
                       style="margin: 0; width: auto; transform: scale(1.2);" />
            </li>
        </ul>

        <div class="buttons">
            <input type="submit" name="saveChanges" value="Сохранить изменения" />
            <input type="submit" formnovalidate name="cancel" value="Отмена" />
        </div>
    </form>

    <?php if (isset($results['user']->id)) { ?>
        <p><a href="admin.php?action=deleteUser&amp;userId=<?php echo $results['user']->id ?>" onclick="return confirm('Удалить этого пользователя?')">
                Удалить пользователя
            </a>
        </p>
    <?php } ?>

<?php include "templates/include/footer.php" ?>