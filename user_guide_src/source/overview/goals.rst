##############################
Design and Architectural Goals
##############################

Our goal for CodeIgniter is maximum performance, capability, and
flexibility in the smallest, lightest possible package.

To meet this goal we are committed to benchmarking, re-factoring, and
simplifying at every step of the development process, rejecting anything
that doesn't further the stated objective.

From a technical and architectural standpoint, CodeIgniter was created
with the following objectives:

-  **Dynamic Instantiation.** In CodeIgniter, components are loaded and
   routines executed only when requested, rather than globally. No
   assumptions are made by the system regarding what may be needed
   beyond the minimal core resources, so the system is very light-weight
   by default. The events, as triggered by the HTTP request, and the
   controllers and views you design will determine what is invoked.
-  **Loose Coupling.** Coupling is the degree to which components of a
   system rely on each other. The less components depend on each other
   the more reusable and flexible the system becomes. Our goal was a
   very loosely coupled system.
-  **Component Singularity.** Singularity is the degree to which
   components have a narrowly focused purpose. In CodeIgniter, each
   class and its functions are highly autonomous in order to allow
   maximum usefulness.

CodeIgniter is a dynamically instantiated, loosely coupled system with
high component singularity. It strives for simplicity, flexibility, and
high performance in a small footprint package.
