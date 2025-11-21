<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

    <h1>Все пользователи</h1>

    <?php if (isset($results['errorMessage'])) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

    <?php if (isset($results['statusMessage'])) { ?>
            <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
    <?php } ?>

    <table>
        <tr>
            <th>Логин</th>
            <th>Статус</th>
            <th>Дата создания</th>
        </tr>

        <?php foreach ($results['users'] as $user) { ?>
            <tr onclick="location='admin.php?action=editUser&amp;userId=<?php echo $user->id?>'">
                <td><?php echo htmlspecialchars($user->login)?></td>
                <td>
                    <?php echo $user->is_active ? '✅ Активен' : '❌ Неактивен' ?>
                </td>
                <td><?php echo date('j M Y', strtotime($user->created_at)) ?></td>
            </tr>
        <?php } ?>
    </table>

    <p><?php echo $results['totalRows']?> пользователь<?php echo ($results['totalRows'] != 1) ? 'ей' : '' ?> всего.</p>

    <p><a href="admin.php?action=newUser">Добавить пользователя</a></p>

<?php include "templates/include/footer.php" ?>