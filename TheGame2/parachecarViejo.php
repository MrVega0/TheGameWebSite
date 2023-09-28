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
function generarMazo()
{
    $mazo = [];
    for ($i = 2; $i <= 99; $i++) {
        $mazo[] = new Card($i);
    }
    return $mazo;
}

$mazo = generarMazo();

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

    <!--Robar cartas/Jalar
    <button onclick="drawCards(2)">Robar cartas</button>-->

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

        var mazo = <?php echo json_encode(array_map(function ($card) {
                        return array('number' => $card->getNumber());
                    }, $mazo)); ?>;
        console.log('Mazo en JavaScript:', mazo);
        var draggedCardId;
        var hand = [];
        var cartasColocadasTotales = 0;
        var button = document.createElement('button');
        var cartasColocadas = 0;
        // Crea un elemento de botón
        button.innerText = 'Robar/Jalar'; // Establece el texto del botón
        // Agrega un evento al botón
        button.addEventListener('click', function() {
            if (cartasColocadas >= 2) {
                drawCards(cartasColocadas);
                cartasColocadas = 0;
            } else {
                alert('Se deben colocar por lo menos 2 cartas antes');
            }
        });

        // Añade el botón al cuerpo del documento
        document.body.appendChild(button);

        function drawCards(numCardsToDraw) {
            // Simula robar cartas del mazo y agregarlas a la mano

            console.log('Intentando robar cartas...');
            for (var i = 0; i < numCardsToDraw; i++) {
                if (mazo.length > 0) {
                    var drawnCard = mazo.pop(); // Obtén la carta del mazo
                    hand.push(drawnCard); // Agrega la carta a la mano
                    console.log('Carta robada:', drawnCard);

                    // Crea un elemento para mostrar la carta en la mano
                    var cardElement = document.createElement('div');
                    cardElement.classList.add('card');
                    cardElement.draggable = true; // Hace la carta arrastrable
                    cardElement.id = 'hand_card_' + (hand.length - 1); // ID único para la carta
                    cardElement.innerText = drawnCard.number;
                    document.getElementById('hand').appendChild(cardElement);

                    // Establece el evento de arrastrar en la carta
                    cardElement.addEventListener('dragstart', drag);
                } else {
                    console.log('No quedan más cartas en el mazo.');
                }
            }
        }

        function removeCardFromHand(cardId) {
            var card = document.getElementById(cardId);
            if (card) {
                card.remove();
            }
        }

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
                    cartasColocadasTotales++;
                    cartasColocadas++;
                    console.log("cartas totales colocadas: ", cartasColocadasTotales);
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
            event.dataTransfer.setData('text/plain', event.target.id);
        }

    </script>

</body>
</html>