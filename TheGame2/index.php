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
?>

<body>

    <div class="board">
        <?php
        // Mostrar las primeras cartas en el tablero (dos cartas con el número 1 y dos cartas con el número 100)
        $primeras_cartas = [
            [1],
            [1],
            [100],
            [100]
        ];

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

        // Define las reglas de colocación en las pilas
        var rules = {
            0: function(cardNumber) {
                return cardNumber > parseInt(document.getElementById('pile_0').innerText);
            },
            1: function(cardNumber) {
                return cardNumber > parseInt(document.getElementById('pile_1').innerText);
            },
            2: function(cardNumber) {
                return cardNumber < parseInt(document.getElementById('pile_2').innerText);
            },
            3: function(cardNumber) {
                return cardNumber < parseInt(document.getElementById('pile_3').innerText);
            }
        };


        function drawCards(numCardsToDraw) {
            console.log('Intentando robar cartas...');
            for (var i = 0; i < numCardsToDraw; i++) {
                if (mazo.length > 0) {
                    var drawnCard = mazo.pop(); // Obtén la carta del mazo
                    hand.push(drawnCard); // Agrega la carta a la mano
                    console.log('Carta robada:', drawnCard);

                    var cardElement = document.createElement('div');
                    cardElement.classList.add('card');
                    cardElement.draggable = true;
                    cardElement.id = 'hand_card_' + (hand.length - 1);
                    cardElement.innerText = drawnCard.number;
                    document.getElementById('hand').appendChild(cardElement);

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

        function checkException(cardNumber, pileNumber) {
    return (cardNumber === pileNumber - 10) || (cardNumber === pileNumber + 10);
}

        function placeCard(pileIndex) {
    if (draggedCardId) {
        var draggedCard = document.getElementById(draggedCardId);
        var pile = document.getElementById(`pile_${pileIndex}`);

        if (pile) {
            var cardNumber = parseInt(draggedCard.innerText);
            var pileNumber = parseInt(pile.innerText);

            // Verifica si la carta puede colocarse según las reglas y la excepción
            if (rules[pileIndex](cardNumber) || checkException(cardNumber, pileNumber)) {
                pile.innerHTML = draggedCard.innerHTML;
                showCardsInPiles();
                removeCardFromHand(draggedCardId);
                cartasColocadasTotales++;
                cartasColocadas++;
                console.log("cartas a regresar: ", cartasColocadas);
                console.log("cartas totales colocadas: ", cartasColocadasTotales);
            } else {
                alert('No se puede colocar esta carta según las reglas.');
                console.log("No se puede colocar esta carta según las reglas.");
            }
        }

        draggedCardId = null;
    }
}


        function drop(event, pileIndex) {
            event.preventDefault();
            placeCard(pileIndex);
        }

        function updatePile(pileIndex, cardValue) {
            var pile = document.getElementById(`pile_${pileIndex}`);
            pile.innerHTML = cardValue;
        }

        function allowDrop(event) {
            event.preventDefault();
        }

        function drag(event) {
            draggedCardId = event.target.id;
            event.dataTransfer.setData('text/plain', event.target.id);
        }

        function showCardsInPiles() {
            for (var i = 0; i < 4; i++) {
                var pile = document.getElementById(`pile_${i}`);
                console.log(`Pila ${i + 1}: ${pile.innerText.trim()}`);
            }
        }

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

</body>

</html>