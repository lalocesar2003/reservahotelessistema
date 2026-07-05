<?php
// Llamamos al controlador para obtener los datos agrupados
$mapaHabitaciones = ControladorHabitaciones::ctrMostrarMapaDisponibilidad();
?>

<div class="content-wrapper" style="min-height: 717px;">

  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Mapa de Disponibilidad (Hoy)</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
            <li class="breadcrumb-item active">Disponibilidad</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      
      <style>
          .grid-habitaciones {
              display: grid;
              grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
              gap: 15px;
          }
          .cuadrado-hab {
              padding: 15px 10px;
              text-align: center;
              color: white;
              border-radius: 8px;
              box-shadow: 0 4px 6px rgba(0,0,0,0.1);
              display: flex;
              flex-direction: column;
              justify-content: center;
              transition: transform 0.2s;
          }
          .cuadrado-hab:hover {
              transform: scale(1.05);
              cursor: default; /* Puedes cambiarlo a 'pointer' si luego les agregas clics */
          }
          .cuadrado-hab .estilo {
              font-size: 1.2rem;
              font-weight: bold;
          }
          .cuadrado-hab .estado {
              font-size: 0.8rem;
              margin-top: 5px;
              text-transform: uppercase;
          }
          .libre { background-color: #28a745; } /* Verde success */
          .ocupada { background-color: #dc3545; } /* Rojo danger */
      </style>

      <?php foreach($mapaHabitaciones as $categoria => $habitaciones): ?>
        
        <div class="card card-primary card-outline mb-4">
          <div class="card-header">
            <h3 class="card-title text-uppercase font-weight-bold">
              <i class="fas fa-bed mr-2"></i> <?= $categoria ?>
            </h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          
          <div class="card-body bg-light">
            <div class="grid-habitaciones">
              <?php foreach($habitaciones as $hab): ?>
                  
                  <?php $claseEstado = ($hab['estado'] == 'libre') ? 'libre' : 'ocupada'; ?>
                  
                  <div class="cuadrado-hab <?= $claseEstado ?>">
                      <span class="estilo"><?= htmlspecialchars($hab['estilo']) ?></span>
                      <span class="estado"><?= $hab['estado'] ?></span>
                  </div>
                  
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      <?php endforeach; ?>

    </div>
  </section>
</div>