<?php

$restaurante = ControladorRestaurante::ctrMostrarRestaurante();

?>

<!--=====================================
RESTAURANTE
======================================-->

<div class="fondoRestaurante container-fluid">


</div>

<div class="restaurante container-fluid pt-5" id="restaurante">
	
	<div class="container">

		<div class="grid-container">
		
			<div class="grid-item carta">
				
				<div class="row p-1 p-lg-5">

					<?php foreach ($restaurante as $key => $value): ?>
	
					<div class="col-6 col-md-4 text-center p-1">
						
						<img src="<?php echo $servidor.$value["img"]; ?>" class="img-fluid w-50 rounded-circle">

						<p class="py-2"><?php echo $value["descripcion"]; ?></p>

					</div>

					<?php endforeach ?>


					<div class="col-12 text-center d-block d-lg-none">
					
						<button class="btn btn-warning text-uppercase mb-5 volverCarta">Volver</button>

					</div>
					
				</div>

			</div>

			<div class="grid-item bloqueRestaurante">
				
				<h1 class="mt-4 my-lg-5">Servicios del Hotel</h1>

				<p class="p-4 my-lg-5">Tu comodidad es nuestra prioridad. Descubre todos los servicios diseñados para que tu estancia sea perfecta, desde la asistencia personalizada hasta nuestras tranquilas áreas comunes. Relájate en un ambiente acogedor y con atención continua.</p>

				<button class="btn btn-warning text-uppercase mb-5 verCarta">Ver la carta</button>

			</div>
			
		</div>		

	</div>

</div>