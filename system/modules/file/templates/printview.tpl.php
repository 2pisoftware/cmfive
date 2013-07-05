<html>
    <body>
        <?$col=0; $row=0;?>
        <?foreach ($attachments as $att):?>
            <?if ($att->isImage()):?>
            <?
                if ($row === 0) {
                    echo "<table>\n";
                }
                if ($col === 0) {
                    echo "<tr>\n";
                }
                $col++;$row++;
                if ($col == $cmax) {
                    echo "</tr>\n";
                    $col = 0;
                }
                if ($row == $rmax) {
                    echo "</table>\n";
                    $row = 0;
                }
            ?>
                <td><img src="<?=$webroot."/file/atfile/".$att->id."/".$att->filename?>" border="0"/></td>
            <?endif;?>
        <?endforeach;?>
        <?
            if ($col != 0) {
                echo "</tr>\n";
            }
            if ($row != 0) {
                echo "</table>\n";
            }
        ?>
    <body>
</html>
