import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (!import.meta.env.SSR) {
    window.axios = axios;
}

export default axios;