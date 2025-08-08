
<style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 700px;
            align-items: start;
            align-content: start;
            align-self: flex-start;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] ,input[type="date"],
        input[type="file"]
        {
            width: 95%;
            padding: 10px;
             border-radius: 5px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }

        fieldset {
            border: 2px solid #ccc;
            border-radius: 5px;
            padding: 13px;
            margin-bottom: 20px;
        }

        legend {
            padding: 0 10px;
            font-weight: bold;
        }
    </style>

<style>
h2 {
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 20px;
}

.form-group {
    margin: 10px;
}

.filter_driver {
    margin: 10px;
}
form select, form button {
    padding: 10px;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}
@media (max-width: 600px) {
    form {
        flex-direction: column;
    }

    .form-group {
        width: 100%;
    }
    
    .filter_driver {
        width: 100%;
    }
}</style>

<style>
.table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background: rgba(100, 149, 237, 0.8); /* CornflowerBlue with transparency */
    backdrop-filter: blur(10px); /* Frosty effect */
    border-radius: 10px;
    overflow: hidden;
}

.table thead {
    background-color: rgb(26, 188, 156); /* Updated header color */
    color: #fff;
}

.table thead th {
    padding: 12px;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
}

.table tbody tr {
    background-color: rgba(245, 245, 245, 0.9); /* Light gray with transparency */
    transition: background-color 0.3s ease;
}

.table tbody tr:nth-child(even) {
    background-color: rgba(220, 230, 240, 0.9); /* LightSteelBlue with transparency */
}

.table tbody tr:hover {
    background-color: rgba(176, 196, 222, 0.9); /* SlateGray with transparency */
}

.table tbody td {
    padding: 12px;
    font-size: 14px;
    color: #2c3e50;
    border-bottom: 1px solid #ddd;
}

.table tbody td:first-child {
    font-weight: bold;
}

.table tbody td:last-child {
    color: #e74c3c;
    font-weight: bold;
}

.table tbody td {
    position: relative;
}

.table tbody td:before {
    content: "";
    position: absolute;
    top: 50%;
    left: -15px;
    transform: translateY(-50%);
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background-color: #2c3e50;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.table tbody tr:hover td:before {
    opacity: 1;
}

.table th, .table td {
    border-right: 1px solid #ddd;
}

.table th:last-child, .table td:last-child {
    border-right: none;
}

</style>

<style>

    select {
    background-color: white;
    color: #2c3e50;
    border: 1px solid rgb(26, 188, 156); /* Matching the table header color */
    border-radius: 5px;
    padding: 8px 12px;
    font-size: 16px;
    appearance: none; /* Removes default arrow */
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

select:hover {
    border-color: rgb(22, 160, 133); /* Slightly darker on hover */
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.5);
}

select:focus {
    outline: none;
    border-color: rgb(22, 160, 133); /* Slightly darker border */
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.8); /* Highlight on focus */
}

select::-ms-expand {
    display: none; /* Hides the default dropdown arrow in IE */
}

select {
    background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="rgb(26,188,156)" d="M2 0L0 2h4zM2 5L0 3h4z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px top 50%;
    background-size: 10px;
}

</style>

<style>
    button {
    background-color: rgb(26, 188, 156); /* Matching the header color */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

button:hover {
    background-color: rgb(22, 160, 133); /* Slightly darker on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

button:active {
    background-color: rgb(19, 141, 117); /* Even darker when clicked */
    transform: scale(0.98); /* Slightly shrink on click */
}

button:focus {
    outline: none;
    box-shadow: 0 0 5px rgb(26, 188, 156);
}

</style>

<style>
        .mainCards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .materialIconsOutlined {
            vertical-align: middle;
            line-height: 1px;
            font-size: 35px;
        }
        
        .myCard {
            width: 80%; /* Adjusted width for 4 cards in a row */
            height: 100px;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            text-decoration: none;
            color: black;
            position: relative;
            overflow: hidden;
            background-color: #1abc9c; /* Same color for all cards */
        }
        
        .cardInner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .cardInner > .materialIconsOutlined {
            font-size: 45px;
        }
        
        .card h1 {
            margin: 20px 0;
        }
        
        .card-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        
        .card-popup {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 10px 0;
            font-size: 16px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .myCard:hover .card-popup {
            opacity: 1;
        }
        
        .myCard:hover {
            opacity: 0.8;
            transform: translateY(-5px);
        }
    </style>
<style>
        /* Inline CSS for quick styling, move to styles.css if needed */
        .sidebar-section {
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
            color: black;
            background-color: #1abc9c;
            border-radius: 5px;
        }
        .sidebar-section:hover {
            background-color: #e0e0e0;
        }
        .sidebar-sublist {
            display: none; /* Initially hide all sections */
            padding-left: 20px;
        }
    </style>
