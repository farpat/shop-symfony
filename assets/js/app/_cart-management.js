import cartService from "./Cart/CartService"

const headCartElement = document.querySelector('#cart-nav')
cartService.loadData(headCartElement)
