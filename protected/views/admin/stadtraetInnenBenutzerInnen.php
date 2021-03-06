<?php
/**
 * @var AdminController $this
 * @var StadtraetIn $stadtraetInnen
 */


/** @var BenutzerIn[] $benutzerInnen */
$benutzerInnen = BenutzerIn::alleAktiveAccounts();
?>
<section class="well">
    <h1>Stadtratsmitglieder: Accounts</h1>

    <form method="POST">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Benutzer*in</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stadtraetInnen as $strIn) {
                /** @var StadtraetIn $strIn */
                ?>
                <tr>
                    <td style="padding-top: 15px;"><?= CHtml::encode($strIn->getName()) ?></td>
                    <td style="padding-top: 15px;"><select name="BenutzerIn[<?= $strIn->id ?>]" title="Zugeordneter BenutzerInnenaccount" size="1">
                            <option value=""></option>
                            <?php
                            foreach ($benutzerInnen as $benutzerIn) {
                                echo '<option value="' . $benutzerIn->id . '"';
                                if ($strIn->benutzerIn_id == $benutzerIn->id) echo ' selected';
                                echo '>' . CHtml::encode($benutzerIn->email) . '</option>';
                            }
                            ?>
                        </select></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <div style="position: fixed; bottom: 0; left: 45%;">
            <button type="submit" class="btn btn-primary" name="<?= AntiXSS::createToken("save") ?>">Speichern</button>
        </div>
    </form>
</section>
