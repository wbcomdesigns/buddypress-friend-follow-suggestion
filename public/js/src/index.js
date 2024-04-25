import ReactDOM from 'react-dom/client'
// import { render } from '@wordpress/element';
import Slider from './components/slider';


document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('swiper-container');
    const dataArgs = root.getAttribute('data-args');
    const args = JSON.parse(dataArgs);

    ReactDOM.createRoot(root).render(
        <Slider args={args} />
    )
});



