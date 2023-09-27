<?php
// Representa una carta
class Card
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }
}

// Genera el mazo con números del 2 al 99
$mazo = [];
for ($i = 2; $i <= 99; $i++) {
    $mazo[] = new Card($i);
}

// Reorganiza aleatoriamente el mazo
shuffle($mazo);

// Tomar las primeras 8 cartas del mazo para la mano
$cartas_sacadas = array_slice($mazo, 0, 8);

// Mostrar las primeras cartas en el tablero
$primeras_cartas = [
    [1],
    [1],
    [100],
    [100]
];
?>
<!DOCTYPE html>
<html>

<head>
    <title>The Game</title>
    <style>
        .card {
            width: 50px;
            height: 75px;
            border: 1px solid black;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 75px;
            cursor: pointer;
        }

        .board {
            margin-bottom: 20px;
        }

        .hand {
            text-align: center;
        }

        .hand .card {
            width: 50px;
            height: 75px;
            font-size: 14px;
        }

        .pile {
            width: 100px;
            height: 125px;
            border: 1px solid black;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 75px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="board">
        <?php
        foreach ($primeras_cartas as $index => $card) {
            echo "<div class='pile' id='pile_$index' ondrop='drop(event, $index)' ondragover='allowDrop(event)'>";
            echo "{$card[0]}";  // Mostrar el número de la carta
            echo "</div>";
        }
        ?>
    </div>


    <div class="hand" id="hand">
        <?php
        foreach ($cartas_sacadas as $index => $card) {
            echo "<div class='card' id='card_$index' draggable='true' ondragstart='drag(event)'>{$card->getNumber()}</div>";
        }
        ?>
    </div>

    <script>
        //este script es para poner cosas en consola
        // Mostrar el mazo en la consola
        console.log("Mazo:", <?php echo json_encode(array_map(function ($card) {
                                    return $card->getNumber();
                                }, $mazo)); ?>);

        // Mostrar la mano en la consola
        console.log("Mano:", <?php echo json_encode(array_map(function ($card) {
                                    return $card->getNumber();
                                }, $cartas_sacadas)); ?>);

        function showCardsInPiles() {
            for (var i = 0; i < 4; i++) {
                var pile = document.getElementById(`pile_${i}`);
                console.log(`Pila ${i + 1}: ${pile.innerText.trim()}`);
            }
        }
    </script>

    <script>
        //script de funciones usables
        var draggedCardId;

        function removeCardFromHand(cardId) {
            var card = document.getElementById(cardId);
            if (card) {
                card.remove();
            }
        }
        /* 
                function placeCardxD(pileIndex) {
                    if (draggedCardId) {
                        var draggedCard = document.getElementById(draggedCardId);
                        var pile = document.getElementById(`pile_${pileIndex}`);

                        if (pile) {
                            // Agrega la carta a la pila
                            pile.innerHTML = draggedCard.innerHTML;
                        }

                        draggedCardId = null; // Limpiar el ID de la carta arrastrada
                        updatePile(pileIndex); // Llama a la función para actualizar la pila después de colocar la carta

                        // Llama a la función para mostrar las cartas en las pilas
                        showCardsInPiles();

                        // Llama a la función para eliminar la carta de la mano
                        removeCardFromHand(draggedCardId);
                    }
                }
        */
        function placeCard(pileIndex) {
            if (draggedCardId) {
                var draggedCard = document.getElementById(draggedCardId);
                var pile = document.getElementById(`pile_${pileIndex}`);

                if (pile) {
                    // Agrega la carta a la pila
                    pile.innerHTML = draggedCard.innerHTML;

                    // Llama a la función para actualizar la pila después de colocar la carta
                    updatePile(pileIndex, draggedCard.innerText);
                    // Llama a la función para mostrar las cartas en las pilas
                    showCardsInPiles();
                    // Llama a la función para eliminar la carta de la mano
                    removeCardFromHand(draggedCardId);
                }

                draggedCardId = null; // Limpiar el ID de la carta arrastrada
            }
        }

        function drop(event, pileIndex) {
            event.preventDefault();
            placeCard(pileIndex); // Llama a la función para colocar la carta en la pila
        }

        function updatePile(pileIndex) {
            var pile = document.getElementById(`pile_${pileIndex}`);
            var cardValue = pile.innerText;

            // Actualiza el número de la carta en la pila
            pile.innerHTML = cardValue;
        }

        function allowDrop(event) {
            event.preventDefault();
        }

        function drag(event) {
            draggedCardId = event.target.id;
        }
    </script>

</body>

</html>