<?php


function room_decode_incluye($incluye){
  if(!is_string($incluye) || trim($incluye) === ''){
    return [];
  }

  $data = json_decode($incluye, true);

  if(json_last_error() === JSON_ERROR_NONE && is_array($data)){
    return $data;
  }

  return [];
}

function room_incluye_texto($incluye){
  $amenidades = room_decode_incluye($incluye);

  if(!empty($amenidades)){
    $items = [];

    foreach($amenidades as $amenidad){
      if(is_array($amenidad) && !empty($amenidad['item'])){
        $items[] = trim($amenidad['item']);
      }
    }

    return implode(' • ', $items);
  }

  return trim((string)$incluye);
}

function room_icono_seguro($icono){
  $icono = trim((string)$icono);

  if($icono === ''){
    return 'fas fa-check';
  }

  return preg_replace('/[^a-zA-Z0-9\-\s]/', '', $icono);
}


$categorias = ControladorCategorias::ctrMostrarCategorias();

function room_text_lower($text){
  return function_exists('mb_strtolower')
    ? mb_strtolower($text, 'UTF-8')
    : strtolower($text);
}

function room_contiene($texto, $busquedas = []){
  foreach($busquedas as $busqueda){
    if(stripos($texto, $busqueda) !== false){
      return true;
    }
  }
  return false;
}

function room_es_banio_compartido($value){
  $texto = room_text_lower(
    ($value['ruta'] ?? '') . ' ' .
    ($value['tipo'] ?? '') . ' ' .
    ($value['descripcion'] ?? '') . ' ' .
    ($value['incluye'] ?? '')
  );

  return room_contiene($texto, ['baño compartido', 'banio compartido', 'compartido', 'compartida']);
}

function room_capacidad($value){
  $texto = room_text_lower(
    ($value['ruta'] ?? '') . ' ' .
    ($value['tipo'] ?? '') . ' ' .
    ($value['descripcion'] ?? '') . ' ' .
    ($value['incluye'] ?? '')
  );

  if(room_contiene($texto, ['ejecutiva', 'ejecutivo'])){
    return 2;
  }

  if(room_contiene($texto, ['triple'])){
    if(room_contiene($texto, ['matrimonial']) && room_contiene($texto, ['2', 'dos'])){
      return 4;
    }
    return 3;
  }

  if(room_contiene($texto, ['doble'])){
    if(room_contiene($texto, ['matrimonial']) && room_contiene($texto, ['1 personal', 'una personal', '+ 1'])){
      return 3;
    }
    return 2;
  }

  if(room_contiene($texto, ['matrimonial'])){
    return 2;
  }

  if(room_contiene($texto, ['personal', 'simple'])){
    return 1;
  }

  return 2;
}

function room_tipo_visible($tipo){
  $tipo = trim($tipo);

  if($tipo === ''){
    return 'Habitación';
  }

  if(stripos($tipo, 'habitación') === false && stripos($tipo, 'habitacion') === false){
    return 'Habitación ' . $tipo;
  }

  return $tipo;
}

?>

<!--=====================================
HABITACIONES (Carrusel 3x)
======================================-->
<?php
  $slides = array_chunk($categorias, 3);
  $carouselId = "carouselHabitaciones";
?>

<style>

.room-amenities{
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 14px;
}

.room-amenity{
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #334155;
  line-height: 1.4;
}

.room-amenity i{
  width: 18px;
  color: #64748b;
  text-align: center;
}

.room-include{
  color: #475569;
  font-size: 15px;
  margin-bottom: 10px;
}
/* ====== Flechas del carrusel ====== */
#<?php echo $carouselId; ?> .carousel-control-prev,
#<?php echo $carouselId; ?> .carousel-control-next{
  width: auto;
  padding: 8px 14px;
  z-index: 20;
}

#<?php echo $carouselId; ?> .carousel-control-prev-icon,
#<?php echo $carouselId; ?> .carousel-control-next-icon{
  width: 48px;
  height: 48px;
  background-color: #111;
  border-radius: 9999px;
  background-size: 50% 50%;
  background-position: center;
  background-repeat: no-repeat;
  box-shadow: 0 8px 24px rgba(0,0,0,.18);
  opacity: 1 !important;
  transition: transform .15s ease, box-shadow .15s ease;
}

#<?php echo $carouselId; ?> .carousel-control-prev-icon{
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='12.5,3 5,10 12.5,17' fill='none' stroke='%23ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
}

#<?php echo $carouselId; ?> .carousel-control-next-icon{
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='7.5,3 15,10 7.5,17' fill='none' stroke='%23ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
}

#<?php echo $carouselId; ?> .carousel-control-prev:hover .carousel-control-prev-icon,
#<?php echo $carouselId; ?> .carousel-control-next:hover .carousel-control-next-icon{
  transform: translateY(-1px) scale(1.02);
  box-shadow: 0 10px 28px rgba(0,0,0,.22);
}

#<?php echo $carouselId; ?> .carousel-control-prev{ left: -6px; }
#<?php echo $carouselId; ?> .carousel-control-next{ right: -6px; }

/* ====== Indicadores ====== */
#<?php echo $carouselId; ?> .carousel-indicators{
  bottom: -45px;
}

#<?php echo $carouselId; ?> .carousel-indicators li{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  border: 0;
  background: #94a3b8;
  opacity: 1;
  margin: 0 5px;
  transition: all .2s ease;
}

#<?php echo $carouselId; ?> .carousel-indicators .active{
  width: 28px;
  background: #0f172a;
}

/* ====== Contenedor general ====== */
.habitaciones{
  background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
  padding-bottom: 70px;
}

.habitaciones .section-subtitle{
  color: #64748b;
  font-size: 18px;
  margin-bottom: 10px;
}

/* ====== Card moderna ====== */
.card-rooms-link{
  text-decoration: none !important;
  color: inherit !important;
  display: block;
  height: 100%;
}

.card-rooms{
  background: #fff;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 14px 32px rgba(0,0,0,.10);
  transition: transform .25s ease, box-shadow .25s ease;
  height: 100%;
}

.card-rooms:hover{
  transform: translateY(-6px);
  box-shadow: 0 18px 40px rgba(0,0,0,.14);
}

.room-media{
  position: relative;
  overflow: hidden;
  background: #e5e7eb;
}

.room-img{
  width: 100%;
  height: 240px;
  object-fit: cover;
  display: block;
  transition: transform .45s ease;
}

.card-rooms:hover .room-img{
  transform: scale(1.05);
}

.room-bath-badge{
  position: absolute;
  top: 14px;
  right: 14px;
  background: #f59e0b;
  color: #fff;
  border-radius: 999px;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 8px 18px rgba(0,0,0,.18);
  z-index: 3;
}

.room-content {
  padding: 22px 20px 20px;
  display: flex;
  flex-direction: column;
  height: calc(100% - 240px);
  background-color: #ffffff !important; /* Forzamos que el fondo de la tarjeta sea blanco */
  color: #0f172a !important; /* Forzamos que el texto por defecto sea oscuro */
}
.room-content p {
  background-color: transparent !important;
  color: inherit;
}
.badge-room{
  border-radius: 999px;
  display: inline-block;
  padding: 8px 16px !important;
  font-size: 12px !important;
  font-weight: 700;
  letter-spacing: .4px;
  width: auto !important;
  margin-bottom: 14px !important;
}

.room-title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
  margin-bottom: 20px;
  background-color: transparent !important; /* Añade esta línea */
}

.room-include{
  color: #475569;
  font-size: 15px;
  margin-bottom: 6px;
}

.room-description {
  color: #64748b !important;
  font-size: 14px !important;
  font-style: italic !important;
  margin-bottom: 18px !important;
  min-height: 20px !important;
  background-color: transparent !important; /* Aseguramos que no tenga fondo */
}

.room-meta{
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-bottom: 20px;
}

.room-meta-item{
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #475569;
}

.room-meta-item i{
  color: #94a3b8;
}

.room-footer{
  margin-top: auto;
  border-top: 1px solid #e2e8f0;
  padding-top: 16px;
}

.room-price-row{
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 12px;
}

.room-price-label{
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
}

.room-price{
  color: #0f172a;
  font-size: 32px;
  font-weight: 900;
  line-height: 1;
  margin: 0;
}

.room-btn{
  margin-top: 16px;
  width: 100%;
  border: 0;
  border-radius: 12px;
  background: #0f172a;
  color: #fff;
  padding: 12px 16px;
  font-size: 15px;
  font-weight: 700;
  text-align: center;
  transition: background .2s ease, transform .2s ease;
}

.card-rooms:hover .room-btn{
  background: #1e293b;
}

@media (max-width: 991.98px){
  .room-img{
    height: 220px;
  }

  .room-content{
    height: calc(100% - 220px);
  }

  .room-title{
    font-size: 24px;
  }

  .room-price{
    font-size: 28px;
  }
}
</style>

<div class="habitaciones container-fluid" id="habitaciones">
  <div class="container">

    <h1 class="pt-4 text-center font-weight-bold">HABITACIONES</h1>
    <p class="section-subtitle text-center">Descubre el confort perfecto para tu estadía</p>

    <div id="<?php echo $carouselId; ?>" class="carousel slide" data-ride="carousel" data-interval="false">

      <!-- Indicadores -->
      <ol class="carousel-indicators">
        <?php foreach ($slides as $i => $_): ?>
          <li data-target="#<?php echo $carouselId; ?>" data-slide-to="<?php echo $i; ?>" class="<?php echo $i===0 ? 'active' : ''; ?>"></li>
        <?php endforeach; ?>
      </ol>

      <!-- Slides -->
      <div class="carousel-inner">

        <?php foreach ($slides as $i => $grupo): ?>
          <div class="carousel-item <?php echo $i===0 ? 'active' : ''; ?>">
            <div class="row p-4 text-center">

              <?php foreach ($grupo as $value): ?>
                <?php
                  $sharedBathroom = room_es_banio_compartido($value);
                  $capacity       = room_capacidad($value);
                  $bathroomLabel  = $sharedBathroom ? 'Baño compartido' : 'Baño privado';

                  $tipoVisible    = room_tipo_visible($value['tipo']);
                 
  $amenidades  = room_decode_incluye($value['incluye']);
  $includeText = room_incluye_texto($value['incluye']);

                  $description    = trim($value['descripcion']) !== '' ? trim($value['descripcion']) : 'Ambiente confortable y moderno';

                  $imgSrc         = $servidor . $value['img'];
                  $roomLink       = $ruta . $value['ruta'];
                ?>

                <div class="col-12 col-md-6 col-lg-4 pb-3 px-0 px-lg-3">
                  <a href="<?php echo htmlspecialchars($roomLink); ?>" class="card-rooms-link">
                    <figure class="card-rooms text-left mb-0">

                      <div class="room-media">
                        <img
                          src="<?php echo htmlspecialchars($imgSrc); ?>"
                          class="img-fluid room-img"
                          alt="<?php echo htmlspecialchars($tipoVisible); ?>"
                        >

                        <?php if($sharedBathroom): ?>
                          <div class="room-bath-badge">
                            <i class="fas fa-bath"></i>
                            <span>Baño compartido</span>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="room-content">

                        <div>
                          <span class="text-white badge-room"
                                style="background:<?php echo htmlspecialchars($value['color']); ?>">
                            <?php echo htmlspecialchars(strtoupper($value['tipo'])); ?>
                          </span>

                          <h3 class="room-title">
                            <?php echo htmlspecialchars($tipoVisible); ?>
                          </h3>

                         <?php if(!empty($amenidades)): ?>
  <div class="room-amenities">
    <?php foreach($amenidades as $amenidad): ?>
      <?php
        $item  = isset($amenidad['item']) ? trim($amenidad['item']) : '';
        $icono = isset($amenidad['icono']) ? room_icono_seguro($amenidad['icono']) : 'fas fa-check';
        if($item === '') continue;
      ?>
      <div class="room-amenity">
        <i class="<?php echo htmlspecialchars($icono); ?>"></i>
        <span><?php echo htmlspecialchars($item); ?></span>
      </div>
    <?php endforeach; ?>
    
  
  </div>
<?php elseif($includeText !== ''): ?>
  <p class="room-include">
    <?php echo htmlspecialchars($includeText); ?>
  </p>
<?php endif; ?>

                         <p class="room-description">
  <?php 
    // strip_tags elimina fondos, colores y etiquetas HTML ocultas
    // htmlspecialchars nos protege de caracteres especiales
    echo htmlspecialchars(strip_tags($description)); 
  ?>
</p>

                          <div class="room-meta">
                            <div class="room-meta-item">
                              <i class="fas fa-users"></i>
                              <span>
                                Hasta <?php echo $capacity; ?>
                                <?php echo $capacity === 1 ? 'persona' : 'personas'; ?>
                              </span>
                            </div>

                            <div class="room-meta-item">
                              <i class="fas fa-bath"></i>
                              <span><?php echo $bathroomLabel; ?></span>
                            </div>
                          </div>
                        </div>

                        <div class="room-footer">
                          <div class="room-price-row">
                            <span class="room-price-label">Por noche</span>
                            <h3 class="room-price">
                              S/<?php echo number_format((float)$value['continental_baja'], 2); ?>
                            </h3>
                          </div>

                          <div class="room-btn">
                            Reservar Ahora
                          </div>
                        </div>

                      </div>
                    </figure>
                  </a>
                </div>
              <?php endforeach; ?>

            </div>
          </div>
        <?php endforeach; ?>

      </div>

      <!-- Controles -->
      <a class="carousel-control-prev" href="#<?php echo $carouselId; ?>" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Anterior</span>
      </a>

      <a class="carousel-control-next" href="#<?php echo $carouselId; ?>" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Siguiente</span>
      </a>

    </div>

  </div>
</div>