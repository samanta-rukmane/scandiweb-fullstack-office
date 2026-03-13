import React, { useState } from "react";
import { Routes, Route } from "react-router-dom";

import Header from "./components/Header";
import ProductList from "./components/ProductList";
import ProductDetails from "./components/ProductDetails";
import CartOverlay from "./components/CartOverlay";
import { GraphQLClient, gql } from "graphql-request";

const client = new GraphQLClient("http://localhost:8000/graphql", {
  headers: {
  },
});

const CREATE_ORDER = gql`
  mutation CreateOrder($items: [OrderItemInput!]!) {
    createOrder(items: $items) {
      id
      total
      items {
        productId
        quantity
        selectedAttributes {
          name
          value
        }
      }
    }
  }
`;

export default function App() {

  const [cartItems, setCartItems] = useState([]);
  const [isCartOpen, setIsCartOpen] = useState(false);


  const addToCart = (product) => {

    setCartItems((prev) => {
      const existing = prev.find(
        (item) =>
          item.product.id === product.id &&
          JSON.stringify(item.product.selectedAttributes) ===
          JSON.stringify(product.selectedAttributes)
      );

      if (existing) {
        return prev.map((item) =>
          item === existing
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      }

      return [...prev, { id: crypto.randomUUID(), product, quantity: 1 }];
    });

    setIsCartOpen(true);
  };


  const changeQuantity = (productId, attributes, delta) => {
    setCartItems((prev) =>
      prev
        .map((item) => {

          const sameProduct =
            item.product.id === productId &&
            JSON.stringify(item.product.selectedAttributes) ===
            JSON.stringify(attributes);

          if (!sameProduct) return item;

          return {
            ...item,
            quantity: Math.max(item.quantity + delta, 0)
          };
        })
        .filter((item) => item.quantity > 0)
    );
  };


  const clearCart = () => {
    setCartItems([]);
  };


  
const placeOrder = async (cartItems) => {
  if (cartItems.length === 0) return;

  try {
    const items = cartItems.map(item => ({
      productId: item.product.id,
      quantity: item.quantity,
      attributes: item.product.selectedAttributes || [],
    }));

    const total = cartItems.reduce(
      (sum, item) => sum + item.product.price * item.quantity,
      0
    );

    await client.request(CREATE_ORDER, { items, total });

    clearCart();
    setIsCartOpen(false);
    alert("Order successfully placed!");
  } catch (error) {
    console.error("Error placing order:", error);
    alert("Order could not be placed. Please try again.");
  }
};


  const total = cartItems.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  );

  const totalItems = cartItems.reduce(
    (sum, item) => sum + item.quantity,
    0
  );


  return (
    <>
      <Header
        totalItems={totalItems}
        openCart={() => setIsCartOpen(true)}
      />

      <Routes>

        <Route
          path="/"
          element={
            <ProductList addToCart={addToCart} />
          }
        />

        <Route
          path="/category/:slug"
          element={
            <ProductList addToCart={addToCart} />
          }
        />

        <Route
          path="/product/:id"
          element={
            <ProductDetails addToCart={addToCart} />
          }
        />

      </Routes>

      {isCartOpen && (
        <CartOverlay
          cartItems={cartItems}
          changeQuantity={changeQuantity}
          total={total}
          clearCart={clearCart}
          placeOrder={placeOrder}
          onClose={() => setIsCartOpen(false)}
        />
      )}

    </>
  );
}