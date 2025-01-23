
function showRegisterForm() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'flex';
}

function showLoginForm() {
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'flex';
}



// Assume we have 10 rooms in total
let totalRooms = 100;
let occupiedRooms = 0;

// Function to update the progress bar and percentage
function updateOccupancy() {
    const occupancyPercentage = (occupiedRooms / totalRooms) * 100;
    document.getElementById('occupancyPercentage').innerText = `${Math.round(occupancyPercentage)}%`;
    document.getElementById('progress').style.width = `${occupancyPercentage}%`;
}

// Simulate a room being occupied
function occupyRoom() {
    if (occupiedRooms < totalRooms) {
        occupiedRooms++;
        updateOccupancy();
    } else {
        alert("All rooms are occupied!");
    }
}

// Simulate a room being vacated
function vacateRoom() {
    if (occupiedRooms > 0) {
        occupiedRooms--;
        updateOccupancy();
    } else {
        alert("No rooms are occupied!");
    }
}

// Initialize the occupancy display
updateOccupancy();
