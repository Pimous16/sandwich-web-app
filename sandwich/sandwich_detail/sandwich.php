<?php
// Charger le fichier JSON contenant les détails des sandwichs
$filename = '../sandwiches.json';
$sandwiches = json_decode(file_get_contents($filename), true);

if (isset($_GET['name'])) {
    $sandwich_name = strtolower($_GET['name']);
} else {
    $sandwich_name = '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($sandwich_name); ?> Détails</title>
    <link rel="stylesheet" href="detail.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <h1><?php echo ucfirst($sandwich_name); ?></h1>

    <form method="post" action="data.php">   
    <table>
        <tr>
            <td class="left">Détails</td>
            <td class="details">
                <input type="hidden" name="sandwich" value="<?php echo ucfirst($sandwich_name); ?>">

                <?php
                if (isset($sandwiches[$sandwich_name])) {
                    echo '<ul>';
                    foreach ($sandwiches[$sandwich_name]['details_sandwich'] as $crudite) {
                        echo "<li>$crudite</li>";
                    }
                    echo '</ul>';
                } else {
                    echo '<p>Le sandwich que vous cherchez n\'existe pas.</p>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><i>Crudités</i></td>
            <td>
                <div class="btn-group">
                    <input type="radio" id="avec" name="crudites" value="avec" required>
                    <label for="avec">avec</label>
                    <span> | </span>
                    <input type="radio" id="sans" name="crudites" value="sans" required>
                    <label for="sans">sans</label>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">Prix: 2,5€</td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="centered-days">
                    <?php
                    $jours = ['Lundi', 'Mardi', 'Jeudi', 'Vendredi'];
                    $aujourd_hui = new DateTime();
                    $heure_limite = new DateTime('11:20');
                    $fin_semaine = new DateTime('this friday 23:59:59');
                    $semaine = [];

                    foreach ($jours as $jour_nom) {
                        $jour = clone $aujourd_hui;

                        switch ($jour_nom) {
                            case 'Lundi': $jour->modify('this week monday'); break;
                            case 'Mardi': $jour->modify('this week tuesday'); break;
                            case 'Jeudi': $jour->modify('this week thursday'); break;
                            case 'Vendredi': $jour->modify('this week friday'); break;
                        }

                        $date_formatee = $jour->format('d/m/Y');
                        $is_disabled = ($jour < $aujourd_hui || ($jour->format('Y-m-d') === $aujourd_hui->format('Y-m-d') && $aujourd_hui > $heure_limite));

                        if ($is_disabled && $jour < $fin_semaine) {
                            $is_disabled = true;
                        }

                        $semaine[$jour_nom] = [
                            'date' => $date_formatee,
                            'disabled' => $is_disabled
                        ];
                    }

                    foreach ($semaine as $jour_nom => $data) {
                        $disabled_attr = $data['disabled'] ? 'disabled' : '';
                        $class_attr = $data['disabled'] ? 'class="grayed-out"' : '';
                        $value = $jour_nom . '|' . $data['date'];

                        echo '<div>';
                        echo '<input type="checkbox" id="' . strtolower($jour_nom) . '" name="jours[]" value="' . $value . '" ' . $disabled_attr . '>';
                        echo '<label for="' . strtolower($jour_nom) . '" ' . $class_attr . '>' . $jour_nom . ' (' . $data['date'] . ')</label>';
                        echo '</div>';
                    }
                    ?>
                    <input type="submit">
                </div>
            </td>
        </tr>
    </table>
    </form>

    <div class="bottom">
        <span class="btn" onclick="history.back()">
            <span class="arrow">←</span> Retour sur le menu
        </span>
    </div>

    <script>
        document.getElementById('confirm-btn')?.addEventListener('click', function() {
            alert('Votre commande pour le sandwich "<?php echo ucfirst($sandwich_name); ?>" a bien été prise en compte !');
        });
    </script>

</body>
</html>
