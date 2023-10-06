<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <title>The Game</title>

</head>

<body>
    <nav>
        <a href="#about">Acerca del Juego</a>
        <a href="#rules">¿Como Jugar?</a>
        <a href="#tablero">Tablero</a>
        <a href="#cards">Cartas</a>
        <a href="#modes">Otros modos</a>
    </nav>
    <div id="logo"><a href="index.php"><img src="images/icono.png" alt="Logo"></a></div>

    <div id="todo">
        <h1>Bienvenido</h1>
        <div class="row" id="botonesJuego" style="display:flex; ">
            <button id="startButton" onclick="startGame()">Iniciar Juego</button>
            <button id="startButton" onclick="continueGame()">Continuar Juego</button>
        </div>

        <section id="about"></section>
        <section style="margin-top: 100px;">
            <h2>Acerca de The Game</h2>
            <p>
                The Game será es su adversario y como equipo deben intentar vencerlo. Aunque las reglas sean sencillas, no será fácil alcanzar el objetivo.
                Deberán descartar la mayor cantidad posible en orden ascendente y descendente,
                pero sin comunicar más de lo permitido. Actúen como equipo y tomen decisiones en conjunto, es la única forma de ganarle a The Game.
            </p>
            <br>
        </section>
        <section id="all">
            <section id="tablero">
                <h2>Reglas y cómo jugar.</h2>

                <h3>Cartas del tablero.</h3>
                <p>El tablero conciste en 4 cartas iniciales, dos cartas con el numero 1 y dos con el 100.</p>
                <div id="tablero-rules">
                    <div class="rowTablero" style="
                                display:flex;
                                line-height:500px;
                        ">
                        <img src="images/1.png" alt="Carta con el numero 1" style="
                                display:flex;
                                height:160px;        
                        ">
                        <img src="images/1.png" alt="Carta con el numero 1" style="
                                display:flex;
                                height:160px;        
                        ">
                    </div>
                    <div class="rowTablero" style="
                                display:flex;
                                line-height:500px;
                        ">
                        <img src="images/100.png" alt="Carta con el numero 100" style="
                                display:flex;
                                height:160px;        
                        ">
                        <img src="images/100.png" alt="Carta con el numero 100" style="
                                display:flex;
                                height:160px;        
                        ">
                    </div>
                </div>
            </section>
            <br><br>
            <section id="cards">
                <h3>Cartas de juego.</h3>
                <p>Las cartas de juego son 98 (cartas con valores del 2 al 99).</p>

                <div class="row">
                    <img src="images/cartas.png" alt="Cartas" style="
                                display:flex;
                                height:130px; 
                        ">
                </div>
            </section>
            <br><br>
            <section id="rules">
                <h3>¿Cómo jugar?</h3>
                <ol>
                    <li>
                        <strong>Objetivo:</strong> El objetivo del juego es colocar todas las cartas en las pilas, en
                        orden ascendente o descendente, según corresponda.
                    </li>
                    <li>
                        <strong>Colocación inicial:</strong> Al principio, estan colocadas dos cartas con el número 1 y dos
                        cartas con el número 100 en el tablero.
                    </li>
                    <li>
                        <strong>Cartas en mano:</strong> Tienes 8 cartas en tu mano para empezar a jugar.
                    </li>
                    <li>
                        <strong>Colocación en pilas:</strong> Debes colocar las cartas de tu mano en las pilas
                        respetando las siguientes reglas:
                        <ul>
                            <li>En las pilas ascendentes (con el número 1), solo puedes colocar cartas con un número mayor que el de
                                la carta superior.</li>
                            <li>En las pilas descendentes (con el número 100), solo puedes colocar cartas con un número menor que el de
                                la carta superior.</li>
                            <li>Si hay una diferencia de 10 (ej. 20 y 30 en cartas descendente o 50 y 40 en orden ascendente), puedes colocar una carta sobre la otra
                                sin importar la dirección de la pila.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Ganar el juego:</strong> Ganas si colocas todas las cartas en las pilas, sin tener
                        ninguna carta restante en tu mano ni en el mazo.
                    </li>
                    <li>
                        <strong>Robar/Jalar cartas:</strong> Puedes robar/jalar más cartas del mazo si has colocado
                        al menos 2 cartas previamente. Si no puedes colocar 2 cartas, no podrás robar/jalar más
                        cartas.
                    </li>
                </ol>
                <div id="descargar-reglas">
                    <p>Para más información, descarga las <a href="documentos/reglas-the-game.pdf" download>reglas</a>.</p>
                </div>
            </section>
        </section>
        <br><br>
        </tbody>
        <script>
            function startGame() {
                document.cookie = "mantener=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "gameState=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                window.location.href = "thegame.php";
            }
            function continueGame() {
                var expDate = new Date();
                expDate.setTime(expDate.getTime() + (2 * 60 * 60 * 1000));
                window.location.href = "thegame.php";
            }
        </script>
</body>

</html>