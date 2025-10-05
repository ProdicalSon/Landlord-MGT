function showRegisterForm() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'flex';
}

function showLoginForm() {
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'flex';
}


let totalRooms = 100;
let occupiedRooms = 0;

function updateOccupancy() {
    const occupancyPercentage = (occupiedRooms / totalRooms) * 100;
    document.getElementById('occupancyPercentage').innerText = `${Math.round(occupancyPercentage)}%`;
    document.getElementById('progress').style.width = `${occupancyPercentage}%`;
}

function occupyRoom() {
    if (occupiedRooms < totalRooms) {
        occupiedRooms++;
        updateOccupancy();
    } else {
        alert("All rooms are occupied!");
    }
}


function vacateRoom() {
    if (occupiedRooms > 0) {
        occupiedRooms--;
        updateOccupancy();
    } else {
        alert("No rooms are occupied!");
    }
}


updateOccupancy();