<?php
session_start();

// Charger le fichier JSON contenant les détails des sandwichs
$filename = '../sandwiches.json';
$sandwiches = [];
if (file_exists($filename)) {
    $sandwiches = json_decode(file_get_contents($filename), true) ?: [];
}

$sandwich_name = '';
$is_valid_sandwich = false;

if (isset($_GET['name'])) {
    $sandwich_name = strtolower(trim($_GET['name']));
    $is_valid_sandwich = isset($sandwiches[$sandwich_name]);
}

if (!$is_valid_sandwich) {
    $error_message = 'Le sandwich demandé est introuvable.';
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

    <h1><?php echo htmlspecialchars(ucfirst($sandwich_name)); ?></h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <a class="btn btn-secondary" href="../index.php">Retour au menu</a>
    <?php else: ?>

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

                    // Commander semaine suivante à partir du vendredi 16h ou weekend
                    $nowNum = (int) $aujourd_hui->format('N'); // 1=Mon ... 7=Dim
                    $aujourd_hui_16h = new DateTime('today 16:00');
                    $nextWeekMode = ($nowNum === 5 && $aujourd_hui >= $aujourd_hui_16h) || $nowNum >= 6;

                    foreach ($jours as $jour_nom) {
                        $jour = clone $aujourd_hui;

                        if ($nextWeekMode) {
                            switch ($jour_nom) {
                                case 'Lundi': $jour->modify('next week monday'); break;
                                case 'Mardi': $jour->modify('next week tuesday'); break;
                                case 'Jeudi': $jour->modify('next week thursday'); break;
                                case 'Vendredi': $jour->modify('next week friday'); break;
                            }
                        } else {
                            switch ($jour_nom) {
                                case 'Lundi': $jour->modify('this week monday'); break;
                                case 'Mardi': $jour->modify('this week tuesday'); break;
                                case 'Jeudi': $jour->modify('this week thursday'); break;
                                case 'Vendredi': $jour->modify('this week friday'); break;
                            }
                        }

                        $date_formatee = $jour->format('d/m/Y');

                        if ($nextWeekMode) {
                            // Pour la semaine suivante, tous les jours sont disponibles (pas de désactivation liée au passé)
                            $is_disabled = false;
                        } else {
                            // Logique actuelle pour la semaine en cours
                            $is_disabled = ($jour < $aujourd_hui || ($jour->format('Y-m-d') === $aujourd_hui->format('Y-m-d') && $aujourd_hui > $heure_limite));

                            if ($is_disabled && $jour < $fin_semaine) {
                                $is_disabled = true;
                            }
                        }

                        $semaine[$jour_nom] = [
                            'date' => $date_formatee,
                            'disabled' => $is_disabled
                        ];
                    }

                    // Afficher un message informatif si on commande pour la semaine suivante
                    if ($nextWeekMode) {
                        echo '<p class="next-week-info">🗓️ Commande pour la semaine prochaine</p>';
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
                    <button type="submit" id="confirm-btn" class="btn btn-primary">Commander</button>
                </div>
            </td>
        </tr>
    </table>
    </form>
    <?php endif; ?>

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
