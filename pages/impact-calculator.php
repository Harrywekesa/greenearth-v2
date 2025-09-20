<!-- pages/impact-calculator.php -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Environmental Impact Calculator</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                See the positive impact of tree planting on the environment
            </p>
        </div>
        
        <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Calculator Form -->
            <div class="bg-gray-50 rounded-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900">Calculate Your Impact</h2>
                <form class="mt-6 space-y-6">
                    <div>
                        <label for="trees" class="block text-sm font-medium text-gray-700">Number of Trees</label>
                        <input type="number" id="trees" name="trees" min="1" value="10" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="years" class="block text-sm font-medium text-gray-700">Growth Period (Years)</label>
                        <select id="years" name="years" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="1">1 Year</option>
                            <option value="5" selected>5 Years</option>
                            <option value="10">10 Years</option>
                            <option value="20">20 Years</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="tree-type" class="block text-sm font-medium text-gray-700">Tree Type</label>
                        <select id="tree-type" name="tree-type" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="mixed">Mixed Species</option>
                            <option value="native">Native Species</option>
                            <option value="fruit">Fruit Trees</option>
                        </select>
                    </div>
                    
                    <button type="button" onclick="calculateImpact()" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Calculate Impact
                    </button>
                </form>
            </div>
            
            <!-- Results -->
            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-2xl font-bold text-gray-900">Estimated Environmental Impact</h2>
                <div id="impact-results" class="mt-6 space-y-6">
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">CO2 Absorption</p>
                            <p class="text-2xl font-bold text-green-600">2,500 kg</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Oxygen Production</p>
                            <p class="text-2xl font-bold text-blue-600">1,800 kg</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Wildlife Habitat</p>
                            <p class="text-2xl font-bold text-yellow-600">15 species</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900">Did You Know?</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        A mature tree can absorb up to 22 kg of CO2 per year and produce enough oxygen for 2 people.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateImpact() {
    const trees = document.getElementById('trees').value || 10;
    const years = document.getElementById('years').value || 5;
    
    // Simple calculations (in a real app, these would be more complex)
    const co2Absorption = trees * 22 * years; // 22kg per tree per year
    const oxygenProduction = trees * 180 * years; // 180kg per tree over 5 years
    const wildlifeHabitat = Math.min(50, Math.floor(trees / 2)); // Up to 50 species
    
    document.getElementById('impact-results').innerHTML = `
        <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
            <div>
                <p class="text-sm text-gray-600">CO2 Absorption</p>
                <p class="text-2xl font-bold text-green-600">${co2Absorption.toLocaleString()} kg</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
            </div>
        </div>
        
        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
            <div>
                <p class="text-sm text-gray-600">Oxygen Production</p>
                <p class="text-2xl font-bold text-blue-600">${oxygenProduction.toLocaleString()} kg</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
            </div>
        </div>
        
        <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg">
            <div>
                <p class="text-sm text-gray-600">Wildlife Habitat</p>
                <p class="text-2xl font-bold text-yellow-600">${wildlifeHabitat} species</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    `;
}
</script>