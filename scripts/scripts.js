var pringao;
var oldLlista;
var lastVoted = 0;
var ifbaned = 0;
var ctx;
var lastBan = 0;
$(document).ready(function () {
	getCurrentUser();
	getAllClients();
	checkban();
	pringao = setInterval(function () {
		getAllClients();
	}, 1500);
	checkbaninterval = setInterval(function () {
		checkban();
	}, 30000);
	$('#vote').click(votacion);
	ctx = document.getElementById('myChart').getContext('2d');
});
function getCurrentUser() {
	$.get('./config/user.php', function (data) {
		$('#user').html('¡Bienvenido <b>' + data.client_nickname + '</b>!'); // data ja es json
		console.log(data);
		if (data.admin) {
		}
	});

}
function getAllClients() {

	$.get('./config/client_list.php', function (data) {
		if (data.error) {
			notOnline(data);
			$('#user').html('¡Bienvenido <b> Pringao</b>!');
			return
		}

		if (JSON.stringify(oldLlista) != JSON.stringify(data)) {
			$('.lista').empty();
			getCurrentUser();
			updateVotes(data);
			$.each(data, function () {

				let client = document.createElement('div');
				$(client).addClass('client');

				let clientName = $(document.createElement('span'))
					.append($(document.createElement('div')).addClass('banquillo').css('filter', 'sepia(100%) saturate(300%) hue-rotate(' + Math.random() * 360 + 'deg) brightness(95%) contrast(80%)'))
					.append(this.client_nickname);

				let votes = $(document.createElement('span')).addClass('votes').html('Votos: ' + this.votes);
				if (data.length > 1) {
					$('#vote').show();
					votes.append($(document.createElement('input')).attr('type', 'radio').attr('name', 'voted').val(this.clid));
					if (this.clid == lastVoted)
						$(client).addClass('votado');
				}
				else {
					$('#vote').hide();
				}
				//$('input[name=voted]')
				$(client).append(clientName).append(votes).click(function () {
					$(this).find('input').prop('checked', 'checked');
				});
				$('.lista').append(client);
			});
			// data ja es json
		}
		oldLlista = data;
	});

}
function notOnline(data) {
	if ($('.lista').children().length > 0)
		$('.lista').empty();

}
function votacion() {
	var clid = $('input:checked[name=voted]').val();
	if (clid != null)
		$.ajax({
			type: "post",
			url: "./config/vote_for.php",
			contentType: 'application/json;charset=utf-8',
			data: JSON.stringify({ voted_clid: clid }),
			success: function (response) {
				//console.log(response);
				$('.client').each(function () { $(this).removeClass('votado') });
				$('input:checked[name=voted]').parent().parent().addClass('votado');
				console.log(response);
				checkban();
			},
			error: function (error) {
				console.log(error);
			}
		});
	lastVoted = clid;
}
function checkban() {
	$.ajax({
		type: "post",
		url: "./config/check_ban_available.php",
		contentType: 'application/json',
		success: function (response) {
			if (response.ban == "true") {
				$('.patada').removeClass('checkban');
			} else {
				$('.patada').addClass('checkban');
			}
			if (lastBan != response.lastBan)
				stats();
			lastBan = response.lastBan;
		},
		error: function (error) {
			console.log(error);
		}
	});
}
function stats() {
	$.ajax({
		type: "post",
		url: "./config/stats.php",
		contentType: 'application/json',
		success: function (response) {
			createChart(response);;
		},
		error: function (jqXHR, exception) {
			console.log(jqXHR, exception);
		}
	});
}
function createChart(data) {
	let users = [];
	let baned_times = [];
	$.each(data, function () {
		users.push(this.user);
		baned_times.push(this.bans);
	});
	myChart = new Chart(ctx, {
		type: 'doughnut',
		data: {
			labels: users,
			datasets: [{
				label: '# of Votes',
				data: baned_times,
				backgroundColor: [
					'rgb(255, 99, 132)',
					'rgb(54, 162, 235)',
					'rgb(255, 206, 86)',
					'rgb(75, 192, 192)',
					'rgb(153, 102, 255)',
					'rgb(255, 159, 64)'
				],
				borderWidth: 0

			}]
		},
		options: {
			responsive: false,
			legend: {
				labels: {
					fontColor: 'black',
					fontSize: 13
				}
			}
		}
	});
}
function prueba() {

}
function updateVotes(userlist) {
	let minimumVotes = Math.round((userlist.length) / 100 * 45) < 3 ? 3 : Math.round((userlist.length) / 100 * 45);
	$('.restantes').html(minimumVotes + ' Votos necesarios.');
}
/*
id ,Name, clid,ip, voted_for , votes, voted_time
*/