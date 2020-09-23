let fs 				= require('fs'),
	config 			= require('./config.js'),
	app             = require('express')(),
	server,
	getProtocolOptions = () => config.https ? {
		protocol: 'https',
		protocolOptions: {
			key: fs.readFileSync(config.ssl.key),
			cert: fs.readFileSync(config.ssl.cert)
		}
	} : {
		protocol: 'http'
	},
    crash           = require('./crash'),
	options 		= getProtocolOptions(),
	fakeStatus		= 0;

	crash.init();

if(options.protocol == 'https') server = require('https').createServer(options.protocolOptions, app); 
else server = require('http').createServer(app);  

let	io              = require('socket.io')(server),
	redis 			= require('redis'),
    redisClient 	= redis.createClient({
		path : '/var/run/redis/redis.sock'
	}),
	requestify 		= require('requestify'),
	acho 			= require('acho'),
	log 			= acho({
		upper: true
	}),
	online 			= 47,
	ipsConnected	= []

server.listen(config.port);
log.info('Локальный сервер запущен на '+ options.protocol + '://' + config.domain + ':' + config.port);

io.sockets.on('connection', function(socket) {
	var address = socket.handshake.address;
	if(!ipsConnected.hasOwnProperty(address)) {
		ipsConnected[address] = 1;
		online = online + 1;
	}
	updateOnline(online);
    socket.on('disconnect', function() {
		if(ipsConnected.hasOwnProperty(address)) {
			delete ipsConnected[address];
			online = online - 1;
		}
		updateOnline(online);
	});
});

function updateOnline(online) {
	io.sockets.emit('online', online);
//	requestify.post(options.protocol + '://' + config.domain + '/api/getOnline')
//	.then(function(response) {
//		var res = JSON.parse(response.body);
//		io.sockets.emit('online', online+res);
//	},function(response){
//		log.error('Ошибка в функции [getOnline]');
//	});
}

redisClient.subscribe('message');
redisClient.subscribe('chat.clear');
redisClient.subscribe('new.msg');
redisClient.subscribe('del.msg');
redisClient.subscribe('ban.msg');
redisClient.subscribe('updateBalance');
redisClient.subscribe('updateBalanceAfter');
redisClient.subscribe('updateBonus');
redisClient.subscribe('updateBonusAfter');
redisClient.subscribe('wheel');
redisClient.subscribe('jackpot.timer');
redisClient.subscribe('jackpot.slider');
redisClient.subscribe('jackpot');
redisClient.subscribe('tower');
redisClient.subscribe('crash');
redisClient.subscribe('new.flip');
redisClient.subscribe('end.flip');
redisClient.subscribe('battle.newBet');
redisClient.subscribe('battle.startTime');
redisClient.subscribe('dice');
redisClient.subscribe('bonus');
redisClient.subscribe('giveaway');
redisClient.subscribe('giveaway.newGiveaway');
redisClient.subscribe('hilo.newBet');
redisClient.subscribe('hilo.timer');
redisClient.on('message', function(channel, message) {
	if(channel == 'chat.clear') io.sockets.emit('clear', JSON.parse(message));
	if(channel == 'new.msg') io.sockets.emit('chat', JSON.parse(message));
	if(channel == 'del.msg') io.sockets.emit('chatdel', JSON.parse(message));
	if(channel == 'ban.msg') io.sockets.emit('ban_message', JSON.parse(message));
	if(channel == 'updateBalanceAfter') {
		message = JSON.parse(message);
		setTimeout(function() {
			io.sockets.emit('updateBalance', message);
		}, message.timer*1000);
	}
	if(channel == 'updateBonusAfter') {
		message = JSON.parse(message);
		setTimeout(function() {
			io.sockets.emit('updateBonus', message);
		}, message.timer*1000);
	}
	if(channel == 'jackpot.timer') {
		message = JSON.parse(message);
		startJackpotTimer(message);
		return;
	}
	if(channel == 'giveaway' && JSON.parse(message).type == 'newGiveaway') {
		message = JSON.parse(message);
		io.sockets.emit('giveaway', {
			type: 'new',
			data: message.data
		});
		startGiveawayTimer(message.data);
		return;
	}
	if(channel == 'battle.startTime') {
		message = JSON.parse(message);
        startBattleTimer(message.time);
        return;
    }
	if(channel == 'wheel' && JSON.parse(message).type == 'wheel_timer') {
		message = JSON.parse(message);
        startWheelTimer(message.timer[2]);
		return;
    }
	if(typeof message == 'string') return io.sockets.emit(channel, JSON.parse(message));
	io.sockets.emit(channel, message);
});

/* Jackpot */

var currentTimers = [];
function startJackpotTimer(res) {
	if(typeof currentTimers[res.room] == 'undefined') currentTimers[res.room] = 0;
	if(currentTimers[res.room] != 0 && (currentTimers[res.room] - new Date().getTime()) < ((res.time+1)*1000)) return;
	currentTimers[res.room] = new Date().getTime();
	let time = res.time;
	let timer = setInterval(() => {
		if(time == 0) {
			clearInterval(timer);
			io.sockets.emit('jackpot', {
				type: 'timer',
				room: res.room,
				data: {
					min: Math.floor(time/60),
					sec: time-(Math.floor(time/60)*60)
				}
			});
			currentTimers[res.room] = 0;
			showJackpotSlider(res.room, res.game);
			return;
		}
		time--;
		io.sockets.emit('jackpot', {
			type: 'timer',
			room: res.room,
			data: {
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			}
		});
	}, 1*1000)
}

function showJackpotSlider(room, game) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/slider', {
		room: room,
		game: game
	})
    .then(function(res) {
		let timeout = setTimeout(() => {
			clearInterval(timeout);
			newJackpotGame(room);
		}, 12*1000)
    }, function(res) {
		log.error('Ошибка в функции slider');
    });
}

function newJackpotGame(room) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/newGame', {
        room : room
    })
    .then(function(res) {
        res = JSON.parse(res.body);
		io.sockets.emit('jackpot', {
			type: 'newGame',
			room: room,
			data: res
		});
    }, function(res) {
		log.error('[ROOM '+room+'] Ошибка в функции newGame');
    });
}

function getStatusJackpot(room) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/getGame', {
        room : room
    })
	.then(function(res) {
		res = JSON.parse(res.body);
		if(res.status == 1) startJackpotTimer(res);
		if(res.status == 2) showJackpotSlider(res.room, res.game);
		if(res.status == 3) newJackpotGame(res.room);
	}, function(res) {
		log.error('[ROOM '+room+'] Ошибка в функции getStatusJackpot');
	});
}

requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/getRooms')
.then(function(res) {
	rooms = JSON.parse(res.body);
	rooms.forEach(function(room) {
		getStatusJackpot(room.name);
	});
}, function(res) {
	log.error(res.body);
	log.error('[APP] Ошибка в функции getRooms');
});

/* Wheel */
function startWheelTimer(time) {
	let timer = setInterval(() => {
		if(time == 0) {
			clearInterval(timer);
			io.sockets.emit('wheel', {
				type: 'timer',
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			});
			showWheelSlider();
			return;
		}
		time--;
		io.sockets.emit('wheel', {
			type: 'timer',
			min: Math.floor(time/60),
			sec: time-(Math.floor(time/60)*60)
		});
	}, 1*1000)
}

function showWheelSlider() {
	requestify.post(options.protocol + '://' + config.domain + '/api/wheel/slider')
    .then(function(res) {
        res = JSON.parse(res.body);
		setTimeout(() => {
            newWheelGame();
        }, res.time);
    }, function(res) {
		log.error('Ошибка в функции wheelSlider');
    });
}

function newWheelGame() {
	requestify.post(options.protocol + '://' + config.domain + '/api/wheel/newGame')
    .then(function(res) {
        res = JSON.parse(res.body);
    }, function(res) {
		log.error('Ошибка в функции wheelNewGame');
    });
}

requestify.post(options.protocol + '://' + config.domain + '/api/wheel/getGame')
.then(function(res) {
	res = JSON.parse(res.body);
	if(res.status == 1) startWheelTimer(res.timer[2]);
	if(res.status == 2) startWheelTimer(res.timer[2]);
	if(res.status == 3) newWheelGame();
}, function(res) {
	log.error('Ошибка в функции wheelGetGame');
});

/*Battle*/
function startBattleTimer(time) {
	setBattleStatus(1);
	let timer = setInterval(() => {
		if(time == 0) {
			clearInterval(timer);
			io.sockets.emit('battle.timer', {
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			});
			setBattleStatus(2);
			showBattleWinners();
			return;
		}
		time--;
		io.sockets.emit('battle.timer', {
			min: Math.floor(time/60),
			sec: time-(Math.floor(time/60)*60)
		});
	}, 1*1000)
}

function showBattleWinners() {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/getSlider')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('battle.slider', res);
		setBattleStatus(3);
		ngTimerBattle();
    }, function(res) {
        log.error('[BATTLE] Ошибка в функции getSlider');
		setTimeout(BattleShowWinners, 1000);
    });
}

function ngTimerBattle() {
	var ngtime = 6;
	var battlengtimer = setInterval(function() {
		ngtime--;
		if(ngtime == 0) {
			clearInterval(battlengtimer);
			newBattleGame();
		}
	}, 1000);
}

function newBattleGame() {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/newGame')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('battle.newGame', res);
    }, function(res) {
        log.error('[BATTLE] Ошибка в функции newGame');
		setTimeout(newBattleGame, 1000);
    });
}

function setBattleStatus(status) {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/setStatus', {
		status : status
    })
    .then(function(res) {
        status = JSON.parse(res.body);
    }, function(res) {
        log.error('[BATTLE] Ошибка в функции setStatus');
		setTimeout(setBattleStatus, 1000);
    });
}

requestify.post(options.protocol + '://' + config.domain + '/api/battle/getStatus')
.then(function(res) {
	res = JSON.parse(res.body);
	if(res.status == 1) startBattleTimer(res.time);
	if(res.status == 2) startBattleTimer(res.time);
	if(res.status == 3) newBattleGame();
}, function(res) {
	log.error('[BATTLE] Ошибка в функции getStatus');
});

function HiloNewGame() {
    requestify.post(options.protocol + '://' + config.domain + '/api/hilo/newGame')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('hilo.newGame', res);
		HiloStartTimer(res.time);
    }, function(res) {
        log.error('[HILO] Ошибка в функции newGame');
		setTimeout(HiloNewGame, 1000);
    });
}

function HiloStartTimer(times) {
	var preFinish = false;
	var hiloTimer,
		time = times*100;
	HiloSetStatus(1);
	clearInterval(hiloTimer);
    hiloTimer = null;
    hiloTimer = setInterval(function() {
		time--;
		if(time <= 0) {
			if(!preFinish) {
				clearInterval(hiloTimer);
				hiloTimer = null;
				preFinish = true;
				HiloSetStatus(2);
				HiloGetFlip();
			}
		}
        io.sockets.emit('hilo.timer', {
            total : times,
            time : 100-(time/100)
        });
    }, 10);
}

function HiloGetFlip() {
    requestify.post(options.protocol + '://' + config.domain + '/api/hilo/getFlip')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('hilo.getFlip', res);
		HiloSetStatus(3);
		setTimeout(HiloNewGame, 4500);
    }, function(res) {
        log.error('[HILO] Ошибка в функции getFlip');
    });
}

// Проверка статусов
requestify.post(options.protocol + '://' + config.domain + '/api/hilo/getStatus')
.then(function(res) {
	res = JSON.parse(res.body);
	if(res.status <= 1) HiloStartTimer(res.time);
	if(res.status == 2) HiloStartTimer(res.time);
	if(res.status == 3) HiloNewGame();
}, function(res) {
	log.error('[HILO] Ошибка в функции getStatus');
});

function HiloSetStatus(status) {
    requestify.post(options.protocol + '://' + config.domain + '/api/hilo/setStatus', {
		status : status
    })
    .then(function(res) {
        status = JSON.parse(res.body);
    }, function(res) {
        log.error('[HILO] Ошибка в функции setStatus');
		setTimeout(HiloSetStatus, 1000);
    });
}

function unBan() {
    requestify.post(options.protocol + '://' + config.domain + '/api/unBan')
    .then(function(res) {
        var data = JSON.parse(res.body);
        setTimeout(unBan, 60000);
    },function(response){
        log.error('Ошибка в функции [unBan]');
        setTimeout(unBan, 1000);
    });
}

function getMerchBalance() {
    requestify.post(options.protocol + '://' + config.domain + '/api/getMerchBalance')
    .then(function(response) {
        var balance = JSON.parse(response.body);
        setTimeout(getMerchBalance, 600000);
    },function(response){
        log.error('Ошибка в функции [getMerchBalance]');
        setTimeout(getMerchBalance, 1000);
    });
}

function getParam() {
    requestify.post(options.protocol + '://' + config.domain + '/api/getParam')
    .then(function(response) {
        var res = JSON.parse(response.body);
		if(res.fake && !fakeStatus) {
			fakeStatus = 1;
			fakeBetJackpot(res.fake);
			fakeBetWheel(res.fake);
			fakeBetDice(res.fake);
			fakeBetBattle(res.fake);
		} else {
			fakeStatus = 0;
			setTimeout(getParam, 5000);
		}
    },function(response){
		log.error(response);
        log.error('Ошибка в функции [fakeStatus]');
        setTimeout(getParam, 1000);
    });
}

function fakeBetJackpot(status) {
	if(status) {
		requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/addBetFake')
		.then(function(res) {
			res = JSON.parse(res.body);
			if(!res.fake) fakeStatus = 0;
			setTimeout(function() {
				fakeBetJackpot(fakeStatus);
			}, Math.round(getRandomArbitrary(5, 17) * 1000));
		}, function(res) {
			log.error('[Jackpot] Ошибка при добавлении ставки!');
			setTimeout(function() {
				fakeBetJackpot(fakeStatus);
			}, Math.round(getRandomArbitrary(5, 17) * 1000));
		});
	} else {
		setTimeout(getParam, 5000);
	}
}

function fakeBetWheel(status) {
	if(status) {
		requestify.post(options.protocol + '://' + config.domain + '/api/wheel/addBetFake')
		.then(function(res) {
			res = JSON.parse(res.body);
			if(!res.fake) fakeStatus = 0;
			setTimeout(function() {
				fakeBetWheel(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 8) * 1000));
		}, function(res) {
			log.error('[Wheel] Ошибка при добавлении ставки!');
			setTimeout(function() {
				fakeBetWheel(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 8) * 1000));
		});
	} else {
		setTimeout(getParam, 5000);
	}
}

function fakeBetDice(status) {
	if(status) {
		requestify.post(options.protocol + '://' + config.domain + '/api/dice/addBetFake')
		.then(function(res) {
			res = JSON.parse(res.body);
			if(!res.fake) fakeStatus = 0;
			setTimeout(function() {
				fakeBetDice(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 3) * 1000));
		}, function(res) {
			log.error('[Dice] Ошибка при добавлении ставки!');
			setTimeout(function() {
				fakeBetDice(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 3) * 1000));
		});
	} else {
		setTimeout(getParam, 5000);
	}
}

function fakeBetBattle(status) {
	if(status) {
		requestify.post(options.protocol + '://' + config.domain + '/api/battle/addBetFake')
		.then(function(res) {
			res = JSON.parse(res.body);
			if(!res.fake) fakeStatus = 0;
			setTimeout(function() {
				fakeBetBattle(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 3) * 1000));
		}, function(res) {
			log.error('[Battle] Ошибка при добавлении ставки!');
			setTimeout(function() {
				fakeBetBattle(fakeStatus);
			}, Math.round(getRandomArbitrary(1, 3) * 1000));
		});
	} else {
		setTimeout(getParam, 5000);
	}
}

function getGiveaway() {
	requestify.post(options.protocol + '://' + config.domain + '/api/giveaway/get')
	.then(function(res) {
		res = JSON.parse(res.body);
		var now = Math.round(new Date().getTime()/1000);
		res.forEach(function(gv) {
			if(now < gv.time_to) startGiveawayTimer(gv);
		})
	}, function(res) {
		log.error('[Giveaway] Ошибка при получении раздач!');
		setTimeout(function() {
			getGiveaway();
		}, 5000);
	});
}

function startGiveawayTimer(giveaway) {
	var now = Math.round(new Date().getTime()/1000);
	var seconds = giveaway.time_to - now;
	
	let gvtimer = setInterval(() => {
		if(seconds == 0) {
			clearInterval(gvtimer);
			io.sockets.emit('giveaway', {
				type: 'timer',
				id: giveaway.id,
				status: giveaway.status,
				hour: (((seconds - seconds % 3600) / 3600) % 60 < 10 ? '0' + ((seconds - seconds % 3600) / 3600) % 60 : ((seconds - seconds % 3600) / 3600) % 60),
				min: (((seconds - seconds % 60) / 60) % 60 < 10 ? '0' + ((seconds - seconds % 60) / 60) % 60 : ((seconds - seconds % 60) / 60) % 60),
				sec: (seconds % 60 < 10 ? '0' + seconds % 60 : seconds % 60)
			});
			setGiveawayWinner(giveaway.id);
			return;
		}
		seconds--;
		io.sockets.emit('giveaway', {
			type: 'timer',
			id: giveaway.id,
			status: giveaway.status,
			hour: (((seconds - seconds % 3600) / 3600) % 60 < 10 ? '0' + ((seconds - seconds % 3600) / 3600) % 60 : ((seconds - seconds % 3600) / 3600) % 60),
			min: (((seconds - seconds % 60) / 60) % 60 < 10 ? '0' + ((seconds - seconds % 60) / 60) % 60 : ((seconds - seconds % 60) / 60) % 60),
			sec: (seconds % 60 < 10 ? '0' + seconds % 60 : seconds % 60)
		});
	}, 1*1000)
}

function setGiveawayWinner(id) {
	requestify.post(options.protocol + '://' + config.domain + '/api/giveaway/end', {
		id : id
    })
	.then(function(res) {
		res = JSON.parse(res.body);
	}, function(res) {
		log.error('[Giveaway] Ошибка при выборе победителя!');
		setTimeout(function() {
			getGiveaway();
		}, 5000);
	});
}

function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}

unBan();
getMerchBalance();
getParam();
getGiveaway();