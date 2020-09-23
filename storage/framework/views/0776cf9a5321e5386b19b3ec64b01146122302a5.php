

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="/css/jackpot.css">
<script type="text/javascript" src="/js/chart.min.js"></script>
<script type="text/javascript" src="/js/chartjs-plugin-labels.js"></script>
<script type="text/javascript" src="/js/jquery.kinetic.min.js"></script>
<script type="text/javascript" src="/js/jackpot.js"></script>


    <!--                <div class="news-block">
                        <strong>Комиссию за пополнение счета через систему Freekassa в честь открытия сайта мы берём на себя!
 </strong>
                        
                    </div> -->
                
    <div class="games-cards-list"><div class="item"><a href="/jackpot" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/jackpot.svg" alt=""></div><div class="card-name">Джекпот</div></a></div><div class="item"><a href="/wheel" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/roulette.svg" alt=""></div><div class="card-name">Х50</div></a></div><div class="item"><a href="/crash" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/crash.png" alt=""></div><div class="card-name">Краш</div></a></div><div class="item"><a href="/battle" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/battle.png" alt=""></div><div class="card-name">Батл</div></a></div><div class="item"><a href="/dice" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/dice.png" alt=""></div><div class="card-name">Дайс</div></a></div><div class="item"><a href="/tower" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/tower.png" alt=""></div><div class="card-name">Башня</div></a></div><div class="item"><a href="/coinflip" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/pvp.png" alt=""></div><div class="card-name">Битва</div></a></div><div class="item"><a href="/" class="game-card"><div class="card-thumb"><img draggable="false" src="/static/media/hilo.png" alt=""></div><div class="card-name">Карты</div></a>
                </div>
               </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/pages/index.blade.php */ ?>