import { Link } from "react-router-dom";
import CategoryList from "./CategoryList";
import Logo from "./Logo";
import CartIcon from "./CartIcon";

export default function Header({ totalItems, openCart }) {
  return (
    <header className="header">
      <div className="container header-inner">

        <CategoryList />

        <div className="logo">
          <Link to="/">
            <Logo />
          </Link>
        </div>

        <button
          data-testid="cart-btn"
          className="cart-button"
          onClick={openCart}
        >
          <CartIcon />
          {totalItems > 0 && (
            <span data-testid="cart-bubble" className="cart-bubble">
              {totalItems}
            </span>
          )}
        </button>

      </div>
    </header>
  );
}