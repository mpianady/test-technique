/**
 * API Service for fetching JSON data
 */
export class APIService {

    static async fetchJSON(url) {
        const response = await fetch(url, {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error(`HTTP ${response.status} for ${url}`);
        return response.json();
    }
}