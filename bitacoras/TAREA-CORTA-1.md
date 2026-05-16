# Kathleen Abigail Argueta Gómez - 20245083

| Smell | Archivo | Método/Clase | Justificación |
|---|---|---|---|
| Switch de estados | Order.php | "notify()" y "DispachSideEffect()" | Considero que los dos metodos están utilizando grandes cadenas de "if" y "elseif" para la misma variable de "evento". Entonces, al agregar otro evento nos estaría haciendo modificar todo denuevo, no lo veo mantenible. |
| Obsesión Primitiva | GodSingleton.php | "log()" | Una mejor práctica sería que en vez de recibir cualquier cadena de texto; existiera un tipo en especifico que admitiera solo los valores que necesita o un enum y no solo colocar "string $message, string $level = 'info'" |
| Envidia de características | OrderController.php | "show()" | Esta redundando mucho en los datos de los usuarios y orden, en vez de estar priorizando la petición de "mostrar" como tal. Bien clarito se aprecia cuando valida el permiso de "$canView" donde referencia clases que nada que ver con el controlador. |
| God class | FacadeThatDoesEverything.php | DeliveryFacade | Primero, esta clase se intuye que es para entregas. Pero me comienza haciendo una finalizacion de compra, despues se pone a ingresar usuarios, generar reportes, wtf, luego metricas y hasta, que se puede malinterpretar pensando estar en lo correcto, asigna repartidores. |
| Intimidad inapropiada | Notification.php | "send()" | La clase se va terminar fundiendo si sigue esa lógica. Primero, está usando "new" dentro del método. Además, lo único que tiene que hacer es saber qué mensaje enviar y a quién. Sin embargo, al usar "new \App\Services\EmailService()", también está hechándose a la espalda fabricar el servicio de correos. |
| Metodo largo | Customer.php | "placeOrder()" | Genuinamente, hace de todo este método; sumándole que tambien tiene un montón de lineas de código que ni terminé de leer. La verdad, es muy fácil identificarlo porque esta usando comentarios para separar todo lo que está realizando. |
